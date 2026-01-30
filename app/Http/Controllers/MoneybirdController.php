<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MoneybirdController extends Controller
{
    protected $client;

    protected $apiUrl;

    protected $accessToken;

    protected $log;

    public function __construct()
    {
        $this->client = new Client;
        $this->apiUrl = 'https://moneybird.com/api/v2/';
        $this->accessToken = config('services.moneybird.token');
        $this->log = Log::channel('moneybird');
    }

    /**
     * Update an existing invoice with tasks.
     *
     * @param  int  $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInvoice($projectId)
    {
        $project = Project::findOrFail($projectId);
        $organisationName = $project->organisation->name;

        $this->log->info('Starting invoice update', [
            'project_id' => $projectId,
            'project_name' => $project->name,
            'organisation_name' => $organisationName,
        ]);

        if (! $this->accessToken) {
            $this->log->error('Moneybird token not configured');

            return response()->json([
                'message' => 'Moneybird token not configured',
                'hint' => 'Set MONEYBIRD_TOKEN in services config',
            ], 500);
        }

        $administrationId = config('services.moneybird.administration_id');
        if (! $administrationId) {
            $this->log->error('Moneybird administration ID not configured');

            return response()->json([
                'message' => 'Moneybird administration ID not configured',
                'hint' => 'Set MONEYBIRD_ADMINISTRATION_ID in services config',
            ], 500);
        }

        // Step 1: Find contact based on the Organisation name
        $contactId = $this->getContactIdByOrganisationName($organisationName);

        if (! $contactId) {
            $this->log->warning('Contact not found in Moneybird', [
                'organisation_name' => $organisationName,
            ]);

            return response()->json([
                'message' => 'Contact not found in Moneybird',
                'organisation_name' => $organisationName,
            ], 404);
        }

        $this->log->info('Found Moneybird contact', [
            'contact_id' => $contactId,
            'organisation_name' => $organisationName,
        ]);

        // Step 2: Get draft sales invoice of contact_id
        $invoiceId = $this->getDraftInvoiceIdByContactId($contactId);

        if (! $invoiceId) {
            $this->log->warning('Draft invoice not found', [
                'contact_id' => $contactId,
                'organisation_name' => $organisationName,
            ]);

            return response()->json([
                'message' => 'Draft invoice not found in Moneybird',
                'contact_id' => $contactId,
                'organisation_name' => $organisationName,
                'hint' => 'Please create a draft invoice in Moneybird for this contact first',
            ], 404);
        }

        $this->log->info('Found draft invoice', [
            'invoice_id' => $invoiceId,
            'contact_id' => $contactId,
        ]);

        // Step 3: Update/patch the sales invoice
        $tasks = $this->getTasksToInvoice($project);

        // Get diagnostic information about tasks
        $allTasksCount = $project->tasks()->count();
        $notInvoicedCount = $project->tasks()->whereNull('invoiced')->count();
        $notServiceCount = $project->tasks()->where('is_service', 0)->count();
        $completedCount = $project->tasks()->whereNotNull('completed_at')->count();

        $this->log->info('Tasks analysis', [
            'project_id' => $projectId,
            'total_tasks' => $allTasksCount,
            'not_invoiced' => $notInvoicedCount,
            'not_service' => $notServiceCount,
            'completed' => $completedCount,
            'invoiceable' => $tasks->count(),
        ]);

        if ($tasks->isEmpty()) {
            $this->log->info('No tasks to invoice', [
                'project_id' => $projectId,
            ]);

            return response()->json([
                'message' => 'No tasks found to invoice',
                'tasks_count' => 0,
                'diagnostics' => [
                    'total_tasks' => $allTasksCount,
                    'not_invoiced' => $notInvoicedCount,
                    'not_service' => $notServiceCount,
                    'completed' => $completedCount,
                    'hint' => 'Tasks must be: not invoiced, not service tasks, and completed',
                ],
            ], 200);
        }

        // Build invoice data with only new tasks (skip any with invoiced date)
        // Filter out any tasks that somehow have an invoiced date (extra safety check)
        $tasksToInvoice = $tasks->filter(function ($task) {
            return is_null($task->invoiced);
        });

        if ($tasksToInvoice->isEmpty()) {
            return response()->json([
                'message' => 'No tasks to invoice (all tasks already have invoiced date)',
                'tasks_found' => $tasks->count(),
            ], 200);
        }

        $ledgerAccountId = config('services.moneybird.ledger_account_id');
        if (! $ledgerAccountId) {
            $this->log->error('Moneybird ledger account ID not configured');

            return response()->json([
                'message' => 'Moneybird ledger account ID not configured',
                'hint' => 'Set MONEYBIRD_LEDGER_ACCOUNT_ID in services config',
            ], 500);
        }

        $invoiceData = [
            'sales_invoice' => [
                'details_attributes' => $tasksToInvoice->map(function ($task) use ($ledgerAccountId) {
                    return [
                        'description' => $task->name.' ['.$task->completed_at->format('d-m-Y').']',
                        'price' => (float) $task->project->hour_tariff,
                        'amount' => (float) round($task->minutes / 60, 2),
                        'ledger_account_id' => $ledgerAccountId,
                    ];
                })->toArray(),
            ],
        ];

        try {
            $this->log->info('Sending invoice update to Moneybird', [
                'invoice_id' => $invoiceId,
                'tasks_count' => $tasksToInvoice->count(),
                'details_count' => count($invoiceData['sales_invoice']['details_attributes']),
                'sample_detail' => ($invoiceData['sales_invoice']['details_attributes'][0] ?? null),
            ]);

            $response = $this->client->patch($this->apiUrl.$administrationId.'/sales_invoices/'.$invoiceId, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $invoiceData,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            $this->log->info('Moneybird response received', [
                'status_code' => $response->getStatusCode(),
                'has_details' => isset($responseBody['sales_invoice']['details']),
                'details_count' => count($responseBody['sales_invoice']['details'] ?? []),
            ]);

            if (isset($responseBody['error']) || isset($responseBody['errors'])) {
                $this->log->error('Moneybird API returned errors', [
                    'response' => $responseBody,
                    'invoice_data' => $invoiceData,
                ]);

                return response()->json([
                    'message' => 'Moneybird API returned errors',
                    'errors' => $responseBody['error'] ?? $responseBody['errors'],
                    'tasks_count' => $tasksToInvoice->count(),
                ], 400);
            }

            if ($response->getStatusCode() === 200) {
                $tasksToInvoice->each->update(['invoiced' => Carbon::now()]);

                $this->log->info('Invoice updated successfully', [
                    'invoice_id' => $invoiceId,
                    'tasks_invoiced' => $tasksToInvoice->count(),
                    'task_ids' => $tasksToInvoice->pluck('id')->toArray(),
                ]);
            }

            return response()->json([
                'message' => 'Invoice updated successfully',
                'tasks_count' => $tasksToInvoice->count(),
                'details_count' => count($invoiceData['sales_invoice']['details_attributes']),
                'data' => $responseBody,
            ]);
        } catch (\Exception $e) {
            $this->log->error('Moneybird invoice update failed', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoiceId,
                'tasks_count' => $tasksToInvoice->count(),
            ]);

            return response()->json([
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage(),
                'tasks_count' => $tasksToInvoice->count(),
            ], 500);
        }
    }

    protected function getContactIdByOrganisationName($organisationName)
    {
        $administrationId = config('services.moneybird.administration_id');

        try {
            $this->log->debug('Searching for contact', [
                'organisation_name' => $organisationName,
            ]);

            $response = $this->client->get($this->apiUrl.$administrationId.'/contacts', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => ['query' => $organisationName],
            ]);

            $contacts = json_decode($response->getBody()->getContents(), true);

            $this->log->debug('Moneybird contacts found', [
                'search_term' => $organisationName,
                'contacts_count' => count($contacts),
                'contact_names' => array_column($contacts, 'company_name'),
            ]);

            // Try exact match first
            foreach ($contacts as $contact) {
                if (isset($contact['company_name']) && $contact['company_name'] === $organisationName) {
                    $this->log->debug('Exact match found', ['contact_id' => $contact['id']]);

                    return $contact['id'];
                }
            }

            // Try case-insensitive match
            foreach ($contacts as $contact) {
                if (isset($contact['company_name']) && strcasecmp($contact['company_name'], $organisationName) === 0) {
                    $this->log->debug('Case-insensitive match found', ['contact_id' => $contact['id']]);

                    return $contact['id'];
                }
            }

            // Try partial match (organisation name contains contact name or vice versa)
            $organisationNameLower = strtolower(trim($organisationName));
            foreach ($contacts as $contact) {
                if (isset($contact['company_name'])) {
                    $contactNameLower = strtolower(trim($contact['company_name']));
                    if (
                        strpos($contactNameLower, $organisationNameLower) !== false ||
                        strpos($organisationNameLower, $contactNameLower) !== false
                    ) {
                        $this->log->debug('Partial match found', [
                            'contact_id' => $contact['id'],
                            'contact_name' => $contact['company_name'],
                        ]);

                        return $contact['id'];
                    }
                }
            }

            $this->log->debug('No matching contact found');
        } catch (\Exception $e) {
            $this->log->error('Failed to search contacts', [
                'error' => $e->getMessage(),
                'organisation_name' => $organisationName,
            ]);

            return null;
        }

        return null;
    }

    protected function getDraftInvoiceIdByContactId($contactId)
    {
        $administrationId = config('services.moneybird.administration_id');

        try {
            $this->log->debug('Searching for draft invoice', [
                'contact_id' => $contactId,
            ]);

            $response = $this->client->get($this->apiUrl.$administrationId.'/sales_invoices', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => ['filter' => 'state:draft,contact_id:'.$contactId],
            ]);

            $invoices = json_decode($response->getBody()->getContents(), true);

            $this->log->debug('Draft invoices found', [
                'contact_id' => $contactId,
                'invoices_count' => count($invoices),
            ]);

            if (! empty($invoices)) {
                return $invoices[0]['id'];
            }
        } catch (\Exception $e) {
            $this->log->error('Failed to search invoices', [
                'error' => $e->getMessage(),
                'contact_id' => $contactId,
            ]);

            return null;
        }

        return null;
    }

    protected function getTasksToInvoice(Project $project): \Illuminate\Database\Eloquent\Collection
    {
        return $project->tasks()
            ->whereNull('invoiced')
            ->whereNotNull('completed_at')
            ->where('is_service', 0)
            ->orderBy('completed_at')
            ->get();
    }
}

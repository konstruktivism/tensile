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

    public function __construct()
    {
        $this->client = new Client;
        $this->apiUrl = 'https://moneybird.com/api/v2/';
        $this->accessToken = env('MONEYBIRD_TOKEN');
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

        // Step 1: Find contact based on the Organisation name
        $contactId = $this->getContactIdByOrganisationName($organisationName);

        if (! $contactId) {
            return response()->json([
                'message' => 'Contact not found in Moneybird',
                'organisation_name' => $organisationName,
            ], 404);
        }

        // Step 2: Get draft sales invoice of contact_id
        $invoiceId = $this->getDraftInvoiceIdByContactId($contactId);

        if (! $invoiceId) {
            return response()->json([
                'message' => 'Draft invoice not found in Moneybird',
                'contact_id' => $contactId,
                'organisation_name' => $organisationName,
                'hint' => 'Please create a draft invoice in Moneybird for this contact first',
            ], 404);
        }

        // Step 3: Update/patch the sales invoice
        $tasks = $this->getTasksToInvoice($project);

        // Get diagnostic information about tasks
        $allTasksCount = $project->tasks()->count();
        $notInvoicedCount = $project->tasks()->whereNull('invoiced')->count();
        $notServiceCount = $project->tasks()->where('is_service', 0)->count();
        $completedCount = $project->tasks()->whereNotNull('completed_at')->count();

        if ($tasks->isEmpty()) {
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

        $invoiceData = [
            'sales_invoice' => [
                'details_attributes' => $tasksToInvoice->map(function ($task) {
                    return [
                        'description' => $task->name . ' [' . $task->completed_at->format('d-m-Y') . ']',
                        'price' => (float) $task->project->hour_tariff,
                        'amount' => (float) round($task->minutes / 60, 2), // Moneybird expects numeric
                        'ledger_account_id' => env('MONEYBIRD_LEDGER_ACCOUNT_ID'),
                    ];
                })->toArray(),
            ],
        ];

        try {
            // Log the request for debugging
            \Log::info('Moneybird invoice update request', [
                'invoice_id' => $invoiceId,
                'tasks_count' => $tasksToInvoice->count(),
                'details_count' => count($invoiceData['sales_invoice']['details_attributes']),
                'sample_detail' => ($invoiceData['sales_invoice']['details_attributes'][0] ?? null),
            ]);

            $response = $this->client->patch($this->apiUrl . env('MONEYBIRD_ADMINISTRATION_ID') . '/sales_invoices/' . $invoiceId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $invoiceData,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Log the response for debugging
            \Log::info('Moneybird invoice update response', [
                'status_code' => $response->getStatusCode(),
                'response_keys' => array_keys($responseBody ?? []),
                'has_details' => isset($responseBody['sales_invoice']['details']),
                'details_count' => count($responseBody['sales_invoice']['details'] ?? []),
            ]);

            // Check for API errors in response
            if (isset($responseBody['error']) || isset($responseBody['errors'])) {
                \Log::error('Moneybird API error', [
                    'response' => $responseBody,
                    'invoice_data' => $invoiceData,
                ]);

                return response()->json([
                    'message' => 'Moneybird API returned errors',
                    'errors' => $responseBody['error'] ?? $responseBody['errors'],
                    'tasks_count' => $tasksToInvoice->count(),
                ], 400);
            }

            // Only mark as invoiced if the API call was successful
            if ($response->getStatusCode() === 200) {
                $tasksToInvoice->each->update(['invoiced' => Carbon::now()]);
            }

            return response()->json([
                'message' => 'Invoice updated successfully',
                'tasks_count' => $tasksToInvoice->count(),
                'details_count' => count($invoiceData['sales_invoice']['details_attributes']),
                'data' => $responseBody,
            ]);
        } catch (\Exception $e) {
            \Log::error('Moneybird invoice update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'invoice_data' => $invoiceData,
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
        try {
            $response = $this->client->get($this->apiUrl . env('MONEYBIRD_ADMINISTRATION_ID') . '/contacts', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => ['query' => $organisationName],
            ]);

            $contacts = json_decode($response->getBody()->getContents(), true);

            // Try exact match first
            foreach ($contacts as $contact) {
                if (isset($contact['company_name']) && $contact['company_name'] === $organisationName) {
                    return $contact['id'];
                }
            }

            // Try case-insensitive match
            foreach ($contacts as $contact) {
                if (isset($contact['company_name']) && strcasecmp($contact['company_name'], $organisationName) === 0) {
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
                        return $contact['id'];
                    }
                }
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    protected function getDraftInvoiceIdByContactId($contactId)
    {
        try {
            $response = $this->client->get($this->apiUrl . env('MONEYBIRD_ADMINISTRATION_ID') . '/sales_invoices', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => ['filter' => 'state:draft,contact_id:' . $contactId],
            ]);

            $invoices = json_decode($response->getBody()->getContents(), true);

            if (! empty($invoices)) {
                return $invoices[0]['id'];
            }
        } catch (\Exception $e) {
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

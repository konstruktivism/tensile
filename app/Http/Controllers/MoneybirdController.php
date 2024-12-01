<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;

class MoneybirdController extends Controller
{
    protected $client;
    protected $apiUrl;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client();
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

        if (!$contactId) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        // Step 2: Get draft sales invoice of contact_id
        $invoiceId = $this->getDraftInvoiceIdByContactId($contactId);

        if (!$invoiceId) {
            return response()->json(['message' => 'Draft invoice not found'], 404);
        }

        // Step 3: Update/patch the sales invoice
        $tasks = $this->getTasksForCurrentMonth($project);

        $invoiceData = [
            'sales_invoice' => [
                'details_attributes' => $tasks->map(function ($task) {
                    return [
                        'description' => $task->name,
                        'price' => $task->project->hour_tariff,
                        'amount' => $task->minutes . ' minutes',
                        'ledger_account_id' => env('MONEYBIRD_LEDGER_ACCOUNT_ID'),
                    ];
                })->toArray(),
            ],
        ];

        try {
            $response = $this->client->patch($this->apiUrl . env('MONEYBIRD_ADMINISTRATION_ID') . '/sales_invoices/' . $invoiceId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $invoiceData,
            ]);

            $tasks->each->update(['invoiced' => Carbon::now()]);


            return response()->json([
                'message' => 'Invoice updated successfully',
                'data' => json_decode($response->getBody()->getContents(), true),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage(),
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

            foreach ($contacts as $contact) {
                if ($contact['company_name'] === $organisationName) {
                    return $contact['id'];
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
            $response = $this->client->get($this->apiUrl . env('MONEYBIRD_ADMINISTRATION_ID') .  '/sales_invoices', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => ['filter' => 'state:draft,contact_id:' . $contactId],
            ]);

            $invoices = json_decode($response->getBody()->getContents(), true);

            if (!empty($invoices)) {
                return $invoices[0]['id'];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    protected function getTasksForCurrentMonth(Project $project): \Illuminate\Database\Eloquent\Collection
    {
        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

        return $project->tasks()
            ->whereBetween('completed_at', [$startOfMonth, $endOfMonth])
            ->whereNull('invoiced')
            ->orderBy('completed_at')
            ->where('is_service', 0)
            ->get();
    }
}

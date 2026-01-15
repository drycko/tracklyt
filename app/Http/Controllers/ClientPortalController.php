<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use Illuminate\Http\Request;

class ClientPortalController extends Controller
{
    /**
     * Show the client dashboard.
     */
    public function dashboard()
    {
        $client = $this->getAuthenticatedClient();

        $stats = [
            'quotes' => $client->quotes()->count(),
            'projects' => $client->projects()->count(),
            'invoices' => $client->invoices()->count(),
            'active_projects' => $client->projects()->where('is_active', true)->count(),
        ];

        return view('client.dashboard', compact('client', 'stats'));
    }

    /**
     * Show client's quotes.
     */
    public function quotes()
    {
        $client = $this->getAuthenticatedClient();
        $quotes = $client->quotes()
                        ->with('lineItems', 'techStack')
                        ->latest()
                        ->paginate(10);

        return view('client.quotes.index', compact('quotes', 'client'));
    }

    /**
     * Show a specific quote.
     */
    public function showQuote(Quote $quote)
    {
        $client = $this->getAuthenticatedClient();

        // Ensure the quote belongs to this client
        if ($quote->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this quote.');
        }

        $quote->load('lineItems', 'techStack');

        return view('client.quotes.show', compact('quote', 'client'));
    }

    /**
     * Download quote PDF.
     */
    public function downloadQuotePdf(Quote $quote)
    {
        $client = $this->getAuthenticatedClient();

        if ($quote->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this quote.');
        }

        // Use the existing quote PDF generation logic
        return app(\App\Http\Controllers\QuoteController::class)->downloadPdf($quote);
    }

    /**
     * Show client's projects.
     */
    public function projects()
    {
        $client = $this->getAuthenticatedClient();
        $projects = $client->projects()
                          ->with('repositories', 'links', 'mobileApps')
                          ->latest()
                          ->paginate(10);

        return view('client.projects.index', compact('projects', 'client'));
    }

    /**
     * Show a specific project.
     */
    public function showProject(Project $project)
    {
        $client = $this->getAuthenticatedClient();

        if ($project->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this project.');
        }

        $project->load('repositories', 'links', 'mobileApps');

        return view('client.projects.show', compact('project', 'client'));
    }

    /**
     * Show client's invoices.
     */
    public function invoices()
    {
        $client = $this->getAuthenticatedClient();
        $invoices = $client->invoices()
                          ->with('project')
                          ->latest('issue_date')
                          ->paginate(10);

        return view('client.invoices.index', compact('invoices', 'client'));
    }

    /**
     * Show a specific invoice.
     */
    public function showInvoice(Invoice $invoice)
    {
        $client = $this->getAuthenticatedClient();

        if ($invoice->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $invoice->load('items', 'project');

        return view('client.invoices.show', compact('invoice', 'client'));
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoicePdf(Invoice $invoice)
    {
        $client = $this->getAuthenticatedClient();

        if ($invoice->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        // Use the existing invoice PDF generation logic
        return app(\App\Http\Controllers\InvoiceController::class)->downloadPdf($invoice);
    }

    /**
     * Get the authenticated client.
     */
    protected function getAuthenticatedClient(): Client
    {
        $clientId = session('client_id');
        
        if (!$clientId) {
            abort(401, 'Not authenticated.');
        }

        $client = Client::find($clientId);

        if (!$client || !$client->is_active) {
            abort(403, 'Client account is not active.');
        }

        return $client;
    }
}

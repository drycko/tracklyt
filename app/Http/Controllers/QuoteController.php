<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quote;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Quote::with('client', 'creator');
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $quotes = $query->latest()->paginate(15);
        
        return view('quotes.index', compact('quotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // if we have client_id in url params, preselect that client
        $preselectedClientId = request()->query('client_id');
        $clients = Client::active()->get();
        return view('quotes.create', compact('clients', 'preselectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'valid_until' => 'nullable|date|after:today',
        ]);

        $validated['quote_number'] = Quote::generateQuoteNumber();
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        $quote = Quote::create($validated);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
        $quote->load('client', 'creator', 'lineItems', 'techStack');
        return view('quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        if (!$quote->canEdit()) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only draft quotes can be edited.');
        }

        $clients = Client::active()->get();
        return view('quotes.edit', compact('quote', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        if (!$quote->canEdit()) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only draft quotes can be edited.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'valid_until' => 'nullable|date|after:today',
        ]);

        $quote->update($validated);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote updated successfully.');
    }

    /**
     * Update quote status.
     */
    public function updateStatus(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,approved,rejected,expired',
        ]);

        $quote->update($validated);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        if (!$quote->canEdit()) {
            return redirect()->route('quotes.index')
                ->with('error', 'Only draft quotes can be deleted.');
        }

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }

    /**
     * Convert quote to project.
     */
    public function convert(Quote $quote)
    {
        if (!$quote->isApproved()) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only approved quotes can be converted to projects.');
        }

        if ($quote->project) {
            return redirect()->route('projects.show', $quote->project)
                ->with('info', 'This quote has already been converted to a project.');
        }

        $project = \App\Models\Project::create([
            'client_id' => $quote->client_id,
            'quote_id' => $quote->id,
            'name' => $quote->title,
            'description' => $quote->description,
            'budget' => $quote->estimated_cost,
            'currency' => $quote->currency,
            'is_active' => true,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Quote converted to project successfully!');
    }

    /**
     * Duplicate an existing quote.
     */
    public function duplicate(Quote $quote)
    {
        $newQuote = $quote->replicate();
        $newQuote->quote_number = Quote::generateQuoteNumber();
        $newQuote->status = 'draft';
        $newQuote->created_by = auth()->id();
        $newQuote->created_at = now();
        $newQuote->updated_at = now();
        $newQuote->save();

        return redirect()->route('quotes.edit', $newQuote)
            ->with('success', 'Quote duplicated successfully. You can now edit it.');
    }

    /**
     * Download quote as PDF.
     */
    public function downloadPdf(Quote $quote)
    {
        $quote->load('client', 'creator');
        
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');
        
        $filename = 'quote-' . $quote->quote_number . '.pdf';
        
        return $pdf->download($filename);
    }
}

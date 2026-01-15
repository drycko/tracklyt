<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('client', 'project')
            ->latest('issue_date')
            ->paginate(15);
        
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::active()->get();
        $projects = Project::active()->get();
        
        return view('invoices.create', compact('clients', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
            'time_entries' => 'nullable|array',
            'time_entries.*' => 'exists:time_entries,id',
            'manual_items' => 'nullable|array',
            'manual_items.*.description' => 'required|string',
            'manual_items.*.quantity' => 'required|numeric|min:0',
            'manual_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Create invoice
            $invoice = Invoice::create([
                'tenant_id' => auth()->user()->tenant_id,
                'client_id' => $validated['client_id'],
                'project_id' => $validated['project_id'],
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'status' => 'draft',
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'tax' => $validated['tax'] ?? 0,
                'currency' => $validated['currency'],
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
                'total' => 0,
            ]);

            // Add time entry items
            if (!empty($validated['time_entries'])) {
                $timeEntries = TimeEntry::with(['task', 'project'])
                    ->whereIn('id', $validated['time_entries'])
                    ->unlocked()
                    ->billable()
                    ->get();

                foreach ($timeEntries as $entry) {
                    $rate = $entry->project->hourly_rate ?? 100;
                    $total = $entry->duration_hours * $rate;

                    InvoiceItem::create([
                        'tenant_id' => auth()->user()->tenant_id,
                        'invoice_id' => $invoice->id,
                        'time_entry_id' => $entry->id,
                        'description' => $entry->task->name . ' - ' . $entry->start_time->format('M d, Y'),
                        'quantity' => $entry->duration_hours,
                        'unit_price' => $rate,
                        'total' => $total,
                    ]);

                    // Lock the time entry
                    $entry->lock();
                }
            }

            // Add manual items
            if (!empty($validated['manual_items'])) {
                foreach ($validated['manual_items'] as $item) {
                    $total = $item['quantity'] * $item['unit_price'];

                    InvoiceItem::create([
                        'tenant_id' => auth()->user()->tenant_id,
                        'invoice_id' => $invoice->id,
                        'time_entry_id' => null,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total' => $total,
                    ]);
                }
            }

            // Recalculate totals
            $invoice->calculateTotals();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('client', 'project', 'items.timeEntry', 'payments');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        if (!$invoice->canEdit()) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Only draft invoices can be edited.');
        }

        $clients = Client::active()->get();
        $projects = Project::active()->get();
        
        return view('invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if (!$invoice->canEdit()) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Only draft invoices can be edited.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Generate invoice from time entries.
     */
    public function generateFromTimeEntries(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'time_entry_ids' => 'required|array',
            'time_entry_ids.*' => 'exists:time_entries,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        try {
            DB::beginTransaction();

            // Get time entries
            $timeEntries = TimeEntry::whereIn('id', $validated['time_entry_ids'])
                ->unlocked()
                ->billable()
                ->with('task', 'project', 'user')
                ->get();

            if ($timeEntries->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No valid billable time entries found.');
            }

            // Create invoice
            $invoice = Invoice::create([
                'client_id' => $validated['client_id'],
                'project_id' => $validated['project_id'],
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'status' => 'draft',
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'tax' => $validated['tax'] ?? 0,
                'currency' => $validated['currency'],
            ]);

            // Create invoice items from time entries
            foreach ($timeEntries as $entry) {
                $hours = round($entry->duration_minutes / 60, 2);
                $rate = $entry->project->hourly_rate ?? $entry->user->hourly_rate ?? 0;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'time_entry_id' => $entry->id,
                    'description' => "{$entry->task->name} - {$entry->notes}",
                    'quantity' => $hours,
                    'unit_price' => $rate,
                ]);

                // Lock the time entry
                $entry->lock();
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', "Invoice created with {$timeEntries->count()} time entries. All entries have been locked.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Show form to select time entries for invoicing.
     */
    public function createFromTimeEntries(Request $request)
    {
        $projectId = $request->get('project_id');
        $clientId = $request->get('client_id');

        $query = TimeEntry::with('user', 'project', 'task')
            ->unlocked()
            ->billable()
            ->whereNotNull('end_time');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($clientId) {
            $query->whereHas('project', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            });
        }

        $timeEntries = $query->latest('start_time')->get();
        $clients = Client::active()->get();
        $projects = Project::active()->get();

        return view('invoices.create-from-time-entries', compact('timeEntries', 'clients', 'projects'));
    }

    /**
     * Update invoice status.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice status updated successfully.');
    }

    /**
     * Mark invoice as sent.
     */
    public function markAsSent(Invoice $invoice)
    {
        $invoice->markAsSent();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice marked as sent.');
    }

    /**
     * Alias for markAsSent (for route compatibility).
     */
    public function markSent(Invoice $invoice)
    {
        return $this->markAsSent($invoice);
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(Invoice $invoice)
    {
        // insert a payment for the full balance
        $invoice->payments()->create([
            'tenant_id' => auth()->user()->tenant_id,
            'created_by' => auth()->id(),
            'payment_date' => now(),
            'amount' => $invoice->balance_due,
            'payment_method' => 'manual',
        ]);
        
        $invoice->markAsPaid();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice marked as paid.');
    }

    /**
     * Alias for markAsPaid (for route compatibility).
     */
    public function markPaid(Invoice $invoice)
    {
        return $this->markAsPaid($invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        if (!$invoice->canEdit()) {
            return redirect()->route('invoices.index')
                ->with('error', 'Only draft invoices can be deleted.');
        }

        // Unlock associated time entries before deleting
        $invoice->items()->each(function ($item) {
            if ($item->timeEntry) {
                $item->timeEntry->unlock();
            }
        });

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully. Time entries have been unlocked.');
    }

    /**
     * Get unbilled time entries for a project (AJAX).
     */
    public function unbilledEntries(Request $request)
    {
        $projectId = $request->get('project_id');
        
        if (!$projectId) {
            return response()->json([]);
        }

        $entries = TimeEntry::with(['user', 'task'])
            ->where('project_id', $projectId)
            ->where('is_billable', true)
            ->whereDoesntHave('invoiceItem')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'date' => $entry->start_time->format('M d, Y'),
                    'user' => $entry->user->name,
                    'task' => $entry->task->name,
                    'hours' => number_format($entry->duration_hours, 2),
                    'rate' => $entry->project->hourly_rate ?? 100,
                    'total' => $entry->duration_hours * ($entry->project->hourly_rate ?? 100),
                ];
            });

        return response()->json($entries);
    }

    /**
     * Download PDF for the invoice.
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'items']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    /**
     * Store a payment for the invoice.
     */
    public function storePayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Check if amount doesn't exceed balance
        $remainingBalance = $invoice->balance_due;
        if ($validated['amount'] > $remainingBalance) {
            return redirect()->back()
                ->with('error', 'Payment amount cannot exceed the remaining balance of ' . number_format($remainingBalance, 2));
        }

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['created_by'] = auth()->id();
        
        $payment = $invoice->payments()->create($validated);

        // Auto-mark as paid if fully paid
        if ($invoice->fresh()->isFullyPaid()) {
            $invoice->markAsPaid();
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Delete a payment.
     */
    public function destroyPayment(Invoice $invoice, InvoicePayment $payment)
    {
        if ($payment->invoice_id !== $invoice->id) {
            abort(404);
        }

        $payment->delete();

        // Update invoice status if needed
        if ($invoice->status === 'paid' && !$invoice->fresh()->isFullyPaid()) {
            $invoice->status = 'sent';
            $invoice->save();
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment deleted successfully.');
    }
}

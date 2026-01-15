<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'project_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'currency',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate totals when items change
        static::saved(function ($invoice) {
            $invoice->calculateTotals();
        });
    }

    /**
     * Get the client that owns the invoice.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the project that this invoice is for.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the items for the invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    /**
     * Get the total amount paid.
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * Get the balance due.
     */
    public function getBalanceDueAttribute(): float
    {
        return max(0, $this->total - $this->total_paid);
    }

    /**
     * Get the payment status.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->total_paid == 0) {
            return 'unpaid';
        } elseif ($this->total_paid >= $this->total) {
            return 'paid';
        } else {
            return 'partially_paid';
        }
    }

    /**
     * Check if invoice is fully paid.
     */
    public function isFullyPaid(): bool
    {
        
        return $this->total_paid >= $this->total;
    }

    /**
     * Calculate and update totals.
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('total');
        
        $this->subtotal = $subtotal;
        $this->total = $subtotal + $this->tax;
        
        $this->saveQuietly(); // Avoid recursion
    }

    /**
     * Check if invoice can be edited.
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['draft']);
    }

    /**
     * Check if invoice is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Mark invoice as sent.
     */
    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->save();
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(): void
    {
        // Only mark as paid if fully paid
        if ($this->isFullyPaid()) {
            $this->status = 'paid';
            $this->save();
        }
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = self::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INV-{$year}-{$newNumber}";
    }

    /**
     * Scope for draft invoices.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for sent invoices.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'sent')
                  ->where('due_date', '<', now());
            });
    }
}

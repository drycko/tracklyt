<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'created_by',
        'quote_number',
        'title',
        'description',
        'status',
        'estimated_hours',
        'estimated_cost',
        'currency',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'estimated_hours' => 'decimal:2',
            'estimated_cost' => 'decimal:2',
            'valid_until' => 'date',
        ];
    }

    /**
     * Get the client that owns the quote.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who created the quote.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the line items for the quote.
     */
    public function lineItems(): HasMany
    {
        return $this->hasMany(QuoteLineItem::class);
    }

    /**
     * Get the tech stack for the quote.
     */
    public function techStack(): HasOne
    {
        return $this->hasOne(QuoteTechStack::class);
    }

    /**
     * Get the project created from this quote.
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    /**
     * Check if quote can be edited.
     */
    public function canEdit(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if quote is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Scope for draft quotes.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for approved quotes.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Generate a unique quote number.
     */
    public static function generateQuoteNumber(): string
    {
        $year = date('Y');
        $lastQuote = self::where('quote_number', 'like', "Q-{$year}-%")
            ->orderBy('quote_number', 'desc')
            ->first();

        if ($lastQuote) {
            $lastNumber = (int) substr($lastQuote->quote_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "Q-{$year}-{$newNumber}";
    }
}

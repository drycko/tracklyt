<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TwilioUsage extends Model
{
    use BelongsToTenant;

    protected $table = 'twilio_usage';

    protected $fillable = [
        'tenant_id',
        'month',
        'whatsapp_count',
        'sms_count',
        'total_messages',
        'whatsapp_cost',
        'sms_cost',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_count' => 'integer',
            'sms_count' => 'integer',
            'total_messages' => 'integer',
            'whatsapp_cost' => 'decimal:4',
            'sms_cost' => 'decimal:4',
            'total_cost' => 'decimal:4',
        ];
    }

    /**
     * Get or create usage record for current month.
     */
    public static function getOrCreateForCurrentMonth(int $tenantId): self
    {
        $month = now()->format('Y-m');

        return static::firstOrCreate(
            ['tenant_id' => $tenantId, 'month' => $month],
            [
                'whatsapp_count' => 0,
                'sms_count' => 0,
                'total_messages' => 0,
                'whatsapp_cost' => 0,
                'sms_cost' => 0,
                'total_cost' => 0,
            ]
        );
    }

    /**
     * Increment WhatsApp usage.
     */
    public function incrementWhatsApp(float $cost = 0): void
    {
        $this->increment('whatsapp_count');
        $this->increment('total_messages');
        
        if ($cost > 0) {
            $this->increment('whatsapp_cost', $cost);
            $this->increment('total_cost', $cost);
        }
    }

    /**
     * Increment SMS usage.
     */
    public function incrementSMS(float $cost = 0): void
    {
        $this->increment('sms_count');
        $this->increment('total_messages');
        
        if ($cost > 0) {
            $this->increment('sms_cost', $cost);
            $this->increment('total_cost', $cost);
        }
    }

    /**
     * Check if tenant has reached Twilio limit.
     */
    public function hasReachedLimit(int $limit): bool
    {
        if ($limit === -1) {
            return false; // Unlimited
        }

        return $this->total_messages >= $limit;
    }

    /**
     * Get remaining messages for the month.
     */
    public function getRemainingMessages(int $limit): int
    {
        if ($limit === -1) {
            return PHP_INT_MAX; // Unlimited
        }

        return max(0, $limit - $this->total_messages);
    }

    /**
     * Scope to get current month's usage.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->where('month', now()->format('Y-m'));
    }

    /**
     * Scope to get specific month's usage.
     */
    public function scopeForMonth($query, string $month)
    {
        return $query->where('month', $month);
    }
}

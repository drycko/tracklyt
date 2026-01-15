<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteTechStack extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'quote_id',
        'language',
        'framework',
        'database',
        'hosting',
        'third_party_services',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'third_party_services' => 'array',
        ];
    }

    /**
     * Get the quote that owns the tech stack.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'quote_id',
        'name',
        'description',
        'project_type',
        'source',
        'billing_type',
        'hourly_rate',
        'estimated_hours',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'estimated_hours' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the client that owns the project.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the quote that this project was created from.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the repositories for the project.
     */
    public function repositories(): HasMany
    {
        return $this->hasMany(ProjectRepository::class);
    }

    /**
     * Get the links for the project.
     */
    public function links(): HasMany
    {
        return $this->hasMany(ProjectLink::class);
    }

    /**
     * Get the tasks for the project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the time entries for the project.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Get the mobile apps for the project.
     */
    public function mobileApps(): HasMany
    {
        return $this->hasMany(MobileAppMetadata::class);
    }

    /**
     * Get the maintenance profile for the project.
     */
    public function maintenanceProfile(): HasOne
    {
        return $this->hasOne(MaintenanceProfile::class);
    }

    /**
     * Scope a query to only include active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if project is from a quote.
     */
    public function isFromQuote(): bool
    {
        return $this->source === 'quote' && $this->quote_id !== null;
    }
}

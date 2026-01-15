<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MobileAppMetadata extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'platform',
        'app_name',
        'package_name',
        'app_store_url',
        'play_store_url',
        'current_version',
    ];

    /**
     * Get the project that owns the mobile app metadata.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the appropriate store URL based on platform.
     */
    public function getStoreUrlAttribute(): ?string
    {
        return $this->platform === 'android' 
            ? $this->play_store_url 
            : $this->app_store_url;
    }

    /**
     * Scope for Android apps.
     */
    public function scopeAndroid($query)
    {
        return $query->where('platform', 'android');
    }

    /**
     * Scope for iOS apps.
     */
    public function scopeIos($query)
    {
        return $query->where('platform', 'ios');
    }
}

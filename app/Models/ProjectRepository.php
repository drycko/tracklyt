<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectRepository extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'provider',
        'repo_url',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure only one primary repository per project
        static::saving(function ($repository) {
            if ($repository->is_primary) {
                static::where('project_id', $repository->project_id)
                    ->where('id', '!=', $repository->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    /**
     * Get the project that owns the repository.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the repository name from URL.
     */
    public function getRepoNameAttribute(): string
    {
        return basename(parse_url($this->repo_url, PHP_URL_PATH));
    }
}

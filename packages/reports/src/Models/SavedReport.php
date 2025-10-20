<?php

namespace Alpha\Reports\Models;

use Alpha\Reports\Services\SaaSykitReportsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'filters',
        'metrics',
        'group_by',
        'date_range',
        'is_public',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'filters' => 'array',
        'metrics' => 'array',
        'group_by' => 'array',
        'date_range' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Get the tenant that owns the report.
     */
    public function tenant()
    {
        return $this->belongsTo(config('reports.tenant_model', 'App\Models\Tenant'), 'tenant_id');
    }

    /**
     * Get the user who created the report.
     */
    public function creator()
    {
        return $this->belongsTo(config('reports.user_model', 'App\Models\User'), 'created_by');
    }

    /**
     * Get the user who last updated the report.
     */
    public function updater()
    {
        return $this->belongsTo(config('reports.user_model', 'App\Models\User'), 'updated_by');
    }

    /**
     * Scope for public reports.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for reports owned by current tenant.
     */
    public function scopeForCurrentTenant($query)
    {
        try {
            $reportsService = app(SaaSykitReportsService::class);
            $tenantId = $reportsService->getCurrentTenantId();

            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }
        } catch (\Throwable $e) {
            // If SaaSykit service is not available, skip tenant filtering
            // This can happen during migrations or early application bootstrap
        }

        return $query;
    }

    /**
     * Check if user can view this report.
     */
    public function canBeViewedBy($user): bool
    {
        if ($this->is_public) {
            return true;
        }

        return $this->tenant_id === $user->tenant_id ?? null;
    }

    /**
     * Check if user can edit this report.
     */
    public function canBeEditedBy($user): bool
    {
        return $this->tenant_id === $user->tenant_id ?? null;
    }
}

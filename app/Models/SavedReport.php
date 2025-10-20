<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'organization_id',
        'organizational_unit_id',
        'name',
        'report_type',
        'configuration',
        'is_public',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function getConfigurationAttribute($value): array
    {
        return json_decode($value, true) ?? [];
    }

    public function setConfigurationAttribute($value): void
    {
        $this->attributes['configuration'] = json_encode($value);
    }

    public function getReportTypeLabelAttribute(): string
    {
        return match ($this->report_type) {
            'advertiser_performance' => 'Advertiser Performance',
            'inventory' => 'Inventory Report',
            'campaign_delivery' => 'Campaign Delivery',
            'revenue' => 'Revenue Report',
            default => 'Unknown',
        };
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeForOrganizationalUnit($query, $ouId)
    {
        return $query->where('organizational_unit_id', $ouId);
    }

    public function scopeForUserOrPublic($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_public', true);
        });
    }

    public function scopeAccessibleBy($query, $user)
    {
        if ($user->is_admin) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('is_public', true)
              ->orWhere('organization_id', $user->organization_id)
              ->orWhere('organizational_unit_id', $user->organizational_unit_id);
        });
    }
}
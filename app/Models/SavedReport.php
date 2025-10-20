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
}
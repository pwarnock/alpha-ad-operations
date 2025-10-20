<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Impression extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_item_id',
        'campaign_id',
        'date',
        'impressions',
        'clicks',
        'revenue',
        'country',
        'device',
        'browser',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'revenue' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function lineItem(): BelongsTo
    {
        return $this->belongsTo(LineItem::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getCtrAttribute(): float
    {
        return $this->impressions > 0 ? ($this->clicks / $this->impressions) * 100 : 0;
    }

    public function getEcpmAttribute(): float
    {
        return $this->impressions > 0 ? ($this->revenue / $this->impressions) * 1000 : 0;
    }

    public function getCpcAttribute(): float
    {
        return $this->clicks > 0 ? $this->revenue / $this->clicks : 0;
    }
}
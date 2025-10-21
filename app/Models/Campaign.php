<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertiser_id',
        'product_id',
        'name',
        'description',
        'status',
        'budget',
        'spent',
        'start_date',
        'end_date',
        'pricing_model',
        'rate',
        'target_url',
        'targeting',
        'notes',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'rate' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'targeting' => 'array',
    ];

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(Advertiser::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function impressions(): HasMany
    {
        return $this->hasMany(Impression::class);
    }

    public function activeLineItems(): HasMany
    {
        return $this->lineItems()->where('status', 'active');
    }

    public function getTotalImpressionsAttribute(): int
    {
        return $this->impressions()->sum('impressions');
    }

    public function getTotalClicksAttribute(): int
    {
        return $this->impressions()->sum('clicks');
    }

    public function getCtrAttribute(): float
    {
        $impressions = $this->total_impressions;
        return $impressions > 0 ? ($this->total_clicks / $impressions) * 100 : 0;
    }

    public function getEcpmAttribute(): float
    {
        $impressions = $this->total_impressions;
        return $impressions > 0 ? ($this->spent / $impressions) * 1000 : 0;
    }

    public function getBudgetUtilizationAttribute(): float
    {
        return $this->budget > 0 ? ($this->spent / $this->budget) * 100 : 0;
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date));
    }

    public function getIsOverdueAttribute(): bool
    {
        return now()->isAfter($this->end_date) && $this->status === 'active';
    }
}
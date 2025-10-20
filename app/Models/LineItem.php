<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'status',
        'impressions_goal',
        'impressions_delivered',
        'clicks_goal',
        'clicks_delivered',
        'budget',
        'spent',
        'start_date',
        'end_date',
        'ad_size',
        'ad_zone',
        'rate',
        'pricing_model',
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

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function impressions(): HasMany
    {
        return $this->hasMany(Impression::class);
    }

    public function getImpressionsRemainingAttribute(): int
    {
        return max(0, $this->impressions_goal - $this->impressions_delivered);
    }

    public function getClicksRemainingAttribute(): int
    {
        return max(0, $this->clicks_goal - $this->clicks_delivered);
    }

    public function getImpressionsPacingAttribute(): float
    {
        $goal = $this->impressions_goal;
        return $goal > 0 ? ($this->impressions_delivered / $goal) * 100 : 0;
    }

    public function getClicksPacingAttribute(): float
    {
        $goal = $this->clicks_goal;
        return $goal > 0 ? ($this->clicks_delivered / $goal) * 100 : 0;
    }

    public function getBudgetUtilizationAttribute(): float
    {
        return $this->budget > 0 ? ($this->spent / $this->budget) * 100 : 0;
    }

    public function getIsUnderDeliveringAttribute(): bool
    {
        $daysElapsed = now()->startOfDay()->diffInDays($this->start_date);
        $totalDays = $this->start_date->diffInDays($this->end_date);
        
        if ($totalDays <= 0 || $daysElapsed <= 0) {
            return false;
        }

        $expectedPacing = ($daysElapsed / $totalDays) * 100;
        return $this->impressions_pacing < ($expectedPacing - 10);
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date));
    }
}
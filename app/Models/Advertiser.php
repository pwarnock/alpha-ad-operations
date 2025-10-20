<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Advertiser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'website',
        'status',
        'credit_limit',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function activeCampaigns(): HasMany
    {
        return $this->campaigns()->where('status', 'active');
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->campaigns()->sum('spent');
    }

    public function getTotalBudgetAttribute(): float
    {
        return $this->campaigns()->sum('budget');
    }

    public function getBudgetUtilizationAttribute(): float
    {
        $totalBudget = $this->total_budget;
        return $totalBudget > 0 ? ($this->total_spent / $totalBudget) * 100 : 0;
    }
}
<?php

namespace Alpha\Reports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'filters',
        'metrics',
        'chart_type',
        'chart_config',
        'is_public',
        'user_id',
        'tenant_id',
    ];

    protected $casts = [
        'filters' => 'array',
        'metrics' => 'array',
        'chart_config' => 'array',
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

    public function getChartConfigAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setChartConfigAttribute($value)
    {
        $this->attributes['chart_config'] = json_encode($value);
    }
}

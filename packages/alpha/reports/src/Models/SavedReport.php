<?php

namespace Alpha\Reports\Models;

use Illuminate\Database\Eloquent\Model;

class SavedReport extends Model
{
    protected $fillable = [
        'name',
        'filters',
        'user_id',
    ];

    protected $casts = [
        'filters' => 'array',
    ];
}

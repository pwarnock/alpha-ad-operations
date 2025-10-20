<?php

namespace App\Filament\Publisher\Resources\LineItemResource\Pages;

use App\Filament\Publisher\Resources\LineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLineItem extends CreateRecord
{
    protected static string $resource = LineItemResource::class;
}
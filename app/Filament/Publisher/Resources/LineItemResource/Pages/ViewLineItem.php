<?php

namespace App\Filament\Publisher\Resources\LineItemResource\Pages;

use App\Filament\Publisher\Resources\LineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLineItem extends ViewRecord
{
    protected static string $resource = LineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
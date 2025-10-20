<?php

namespace App\Filament\Publisher\Resources\LineItemResource\Pages;

use App\Filament\Publisher\Resources\LineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLineItems extends ListRecords
{
    protected static string $resource = LineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
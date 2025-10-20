<?php

namespace App\Filament\Publisher\Resources\LineItemResource\Pages;

use App\Filament\Publisher\Resources\LineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLineItem extends EditRecord
{
    protected static string $resource = LineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
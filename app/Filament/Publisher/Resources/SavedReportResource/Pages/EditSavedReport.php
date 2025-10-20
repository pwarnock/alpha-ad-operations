<?php

namespace App\Filament\Publisher\Resources\SavedReportResource\Pages;

use App\Filament\Publisher\Resources\SavedReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSavedReport extends EditRecord
{
    protected static string $resource = SavedReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
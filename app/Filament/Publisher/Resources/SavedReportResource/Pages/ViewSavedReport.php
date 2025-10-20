<?php

namespace App\Filament\Publisher\Resources\SavedReportResource\Pages;

use App\Filament\Publisher\Resources\SavedReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSavedReport extends ViewRecord
{
    protected static string $resource = SavedReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
<?php

namespace Alpha\Reports\Filament\Resources\SavedReportResource\Pages;

use Alpha\Reports\Filament\Resources\SavedReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSavedReports extends ListRecords
{
    protected static string $resource = SavedReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace Alpha\Reports\Filament\Resources\SavedReportResource\Pages;

use Alpha\Reports\Filament\Resources\SavedReportResource;
use Alpha\Reports\Services\SaaSykitReportsService;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}

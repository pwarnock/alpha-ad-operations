<?php

namespace Alpha\Reports\Filament\Resources\SavedReportResource\Pages;

use Alpha\Reports\Filament\Resources\SavedReportResource;
use Alpha\Reports\Services\SaaSykitReportsService;
use Filament\Resources\Pages\CreateRecord;

class CreateSavedReport extends CreateRecord
{
    protected static string $resource = SavedReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $reportsService = app(SaaSykitReportsService::class);

        $data['tenant_id'] = $reportsService->getCurrentTenantId();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}

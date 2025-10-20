<?php

namespace App\Filament\Resources\SavedReportResource\Pages;

use App\Filament\Resources\SavedReportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSavedReport extends CreateRecord
{
    protected static string $resource = SavedReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        \Illuminate\Support\Facades\Log::info('mutating data', $data);
        $data['user_id'] = auth()->id();
        return $data;
    }
}
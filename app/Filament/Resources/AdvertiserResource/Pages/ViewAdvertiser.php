<?php

namespace App\Filament\Resources\AdvertiserResource\Pages;

use App\Filament\Resources\AdvertiserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdvertiser extends ViewRecord
{
    protected static string $resource = AdvertiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
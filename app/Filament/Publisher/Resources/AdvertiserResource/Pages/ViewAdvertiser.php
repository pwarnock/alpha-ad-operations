<?php

namespace App\Filament\Publisher\Resources\AdvertiserResource\Pages;

use App\Filament\Publisher\Resources\AdvertiserResource;
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
<?php

namespace App\Filament\Publisher\Resources\AdvertiserResource\Pages;

use App\Filament\Publisher\Resources\AdvertiserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdvertisers extends ListRecords
{
    protected static string $resource = AdvertiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
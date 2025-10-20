<?php

namespace App\Filament\Publisher\Resources\AdvertiserResource\Pages;

use App\Filament\Publisher\Resources\AdvertiserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvertiser extends EditRecord
{
    protected static string $resource = AdvertiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
<?php

namespace App\Filament\Resources\PackingTypeResource\Pages;

use App\Filament\Resources\PackingTypeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePackingType extends CreateRecord
{
    protected static string $resource = PackingTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?$this->getResource()::getUrl('index') : $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Packing Type Creation")
        ->body("The packing type has been created successfully");
    }
}

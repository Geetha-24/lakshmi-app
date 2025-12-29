<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?$this->getResource()::getUrl('index') : $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Unit Creation")
        ->body("The unit has been created successfully");
    }
}

<?php

namespace App\Filament\Resources\MarginConfigurationResource\Pages;

use App\Filament\Resources\MarginConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMarginConfiguration extends CreateRecord
{
    protected static string $resource = MarginConfigurationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Margin Creation")
        ->body("The Margin  has been created successfully");
    }
}

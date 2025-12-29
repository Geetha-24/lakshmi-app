<?php

namespace App\Filament\Resources\MarginConfigurationResource\Pages;

use App\Filament\Resources\MarginConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;


class EditMarginConfiguration extends EditRecord
{
    protected static string $resource = MarginConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Product Updation")
        ->body("The product has been created successfully");
    }
}

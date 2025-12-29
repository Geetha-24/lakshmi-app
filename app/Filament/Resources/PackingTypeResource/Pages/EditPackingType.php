<?php

namespace App\Filament\Resources\PackingTypeResource\Pages;

use App\Filament\Resources\PackingTypeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPackingType extends EditRecord
{
    protected static string $resource = PackingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

     protected function getRedirectUrl(): string
    {
        return $this->previousUrl ? $this->getResource()::getUrl('index') : $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Packing Type Updation")
        ->body("The packing type has been updated successfully");
    
    }
}

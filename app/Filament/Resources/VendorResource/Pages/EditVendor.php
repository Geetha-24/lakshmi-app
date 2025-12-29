<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

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
        ->title("Vendor Updation")
        ->body("The Vendor has been updated successfully");
    
    }

}

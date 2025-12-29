<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

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
        ->title("Unit Updation")
        ->body("The unit has been updated successfully");
    
    }
}

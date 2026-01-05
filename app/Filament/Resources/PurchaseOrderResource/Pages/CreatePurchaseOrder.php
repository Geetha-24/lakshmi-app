<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;


    protected function getRedirectUrl(): string
    {
        return route(
        'filament.admin.resources.purchase-orders.view',
        ['record' => $this->record->id]
        );
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Purchase Order")
        ->body("The Purchase order has been created successfully");
    }
}

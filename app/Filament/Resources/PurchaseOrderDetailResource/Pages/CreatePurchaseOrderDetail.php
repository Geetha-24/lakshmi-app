<?php

namespace App\Filament\Resources\PurchaseOrderDetailResource\Pages;

use App\Filament\Resources\PurchaseOrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePurchaseOrderDetail extends CreateRecord
{
    protected static string $resource = PurchaseOrderDetailResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! ($data['apply_tax'] ?? false)) {
            $data['tax_percentage'] = 0;
            $data['tax_amount'] = 0;
            $data['inc_tax_total_amount'] = $data['total_amount'];
        }

        return $data;
    }

    


   protected function getRedirectUrl(): string
    {
        return route(
        'filament.admin.resources.purchase-orders.view',
        ['record' => $this->record->po_id]
        );
    }


    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Order Details")
        ->body("The Purchase order has been created successfully");

    }
}

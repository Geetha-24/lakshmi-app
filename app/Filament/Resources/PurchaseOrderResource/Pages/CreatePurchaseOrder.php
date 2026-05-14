<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\VendorLedger;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;

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

    protected function  afterCreate(): void
    {
        // 2️⃣ Vendor ledger CREDIT
        $record = $this->record;

        PurchaseOrder::where('id',$record->id)->update(['due_amount'=>$record->net_amount])->save();

        VendorLedger::create([
            'vendor_id'      => $record->vendor_id,
            'date'           => $record->invoice_date,
            'reference_type' => 'purchase_invoice',
            'reference_id'   => $record->id,
            'credit'         => $record->net_amount,
        ]);
    }

}

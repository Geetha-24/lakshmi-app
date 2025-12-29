<?php

namespace App\Filament\Resources\PurchaseOrderDetailResource\Pages;

use App\Filament\Resources\PurchaseOrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
}

<?php

namespace App\Filament\Resources\PurchaseOrderDetailResource\Pages;

use App\Filament\Resources\PurchaseOrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseOrderDetails extends ListRecords
{
    protected static string $resource = PurchaseOrderDetailResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}

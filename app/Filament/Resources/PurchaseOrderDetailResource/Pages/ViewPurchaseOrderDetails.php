<?php

namespace App\Filament\Resources\PurchaseOrderDetailResource\Pages;

use App\Filament\Resources\PurchaseOrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;

class ViewPurchaseOrderDetails extends ViewRecord
{
    protected static string $resource = PurchaseOrderDetailResource::class;

   public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Basic Info')->columns(2)->schema([
                Infolists\Components\TextEntry::make('po_id')->label('Purchase Order'),
                Infolists\Components\TextEntry::make('ProductVariant.p_name'),
                Infolists\Components\TextEntry::make('qunatity'),
                Infolists\Components\TextEntry::make('unit_price')->label('Unit Price'),
                Infolists\Components\TextEntry::make('total_amount')->label('Total Amount'),
               

                ]),
                Section::make('Tax Info')->columns(3)->schema([
                Infolists\Components\TextEntry::make('tax_percentage')->label('Tax %'),
                Infolists\Components\TextEntry::make('tax_amount')->label('Tax Total'),
                Infolists\Components\TextEntry::make('inc_tax_total_amount')->label('Total Amount(Incl Tax)'),
                ]),
                 Section::make('Costing Rule')->columns(3)->schema([
                Infolists\Components\TextEntry::make('delivery_charge_per_unit')->label('Delivery Charge'),
                Infolists\Components\TextEntry::make('lorry_charge_per_unit')->label('Lorry Charge'),
                Infolists\Components\TextEntry::make('profit_value')->label('Fixed Profit'),
                Infolists\Components\TextEntry::make('SP_with_profit')->label('Selling Price(Incl Profit)'),
                Infolists\Components\TextEntry::make('SP_without_profit')->label('Selling Price'),

                 ]),

                 Section::make('Stock Posting Info ')->schema([
                Infolists\Components\TextEntry::make('posted_qty')->label('Posted Quantity'),
                Infolists\Components\TextEntry::make('posted_at')->label('Post at'),

                 ]),
                




            ]);
    }
}

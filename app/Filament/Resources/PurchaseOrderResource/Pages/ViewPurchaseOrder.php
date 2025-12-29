<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PurchaseOrderResource\Widgets\PurchaseOrderDetailsTable;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->record($this->record) // 🔥 THIS IS REQUIRED
            ->schema([
            Section::make('Bill Information')->columns(2)
            ->columnSpan(2)
                ->schema([
                    TextEntry::make('invoice_number'),
                    TextEntry::make('invoice_date'),
                    TextEntry::make('net_amount'),
                    TextEntry::make('paid_amount'),
                    TextEntry::make('due_amount'),
                ]),
        ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            PurchaseOrderDetailsTable::class,
        ];
    }
    protected function getTableContentHeight(): ?string
    {
        return 'calc(100vh - 300px)';
    }
}

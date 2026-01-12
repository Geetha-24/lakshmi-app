<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Filament\Resources\PurchaseOrderResource\Widgets\PurchaseOrderStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;


class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;
       
    protected function getHeaderWidgets(): array
    {
        return [
            PurchaseOrderStats::class,
        ];
    }

    protected function getWidgetsColumns(): int
    {
        return 4;
    }

    protected function getHeaderActions(): array
    {
        return [
             Actions\CreateAction::make(),
            \Filament\Actions\Action::make('filters')
                ->label('Filter Summary')
                ->form([
                    Select::make('month')
                        ->label('Month')
                        ->options([
                            '01' => 'January',
                            '02' => 'February',
                            '03' => 'March',
                            '04' => 'April',
                            '05' => 'May',
                            '06' => 'June',
                            '07' => 'July',
                            '08' => 'August',
                            '09' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December',
                        ])
                        ->reactive(),

                    DatePicker::make('from_date')
                        ->label('From Date')
                        ->reactive(),

                    DatePicker::make('to_date')
                        ->label('To Date')
                        ->reactive(),
                ])
                ->action(fn (array $data) => $this->dispatchFilters($data)),
        ];
    }

    protected function dispatchFilters(array $data): void
    {
        $this->dispatch('po-filters-updated', $data);
    }

    
}

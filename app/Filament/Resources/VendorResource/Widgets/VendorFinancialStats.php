<?php

namespace App\Filament\Resources\VendorResource\Widgets;

use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;

class VendorFinancialStats extends StatsOverviewWidget
{

        public ?Vendor $record = null;
        protected static bool $isLazy = false;

    protected function getStats(): array
    {

         return [
        Stat::make('Total Amount', $this->record->purchases()->sum('net_amount'))->color('success')->description('All Purchase'),
        Stat::make('Total Paid', $this->record->payments()->sum('allocated_amount'))->color('primary')->description('Debited Amount'),
        Stat::make(
            'Net Payable',
            $this->record->purchases()->sum('due_amount')
            - $this->record->payments()->sum('unallocated_amount')
        )->color('danger')->description('Balance Amount'),
    ];
        
    }
}

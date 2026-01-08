<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use App\Models\Customer;

class CustomerFinancialStats extends StatsOverviewWidget
{
        public ?Customer $record = null;
        protected static bool $isLazy = false;



     protected function getStats(): array
     {
     
        $totalSales = $this->record->SalesOrder()->sum('total_amount');
        $totalPaid  = $this->record->payment()->sum('amount');
        $outstanding = $totalSales - $totalPaid;

        return [
            Stat::make('Total Sales', number_format($totalSales, 2))
                ->description('All sales')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 hover:shadow-md transition',
                ]),

            Stat::make('Total Paid', number_format($totalPaid, 2))
                ->description('Received')
                ->color('success'),

            Stat::make('Outstanding', number_format($outstanding, 2))
                ->description('Balance due')
                ->color($outstanding > 0 ? 'danger' : 'success'),

            Stat::make(
                'Last Payment',
                optional(
                    $this->record->payment()->latest()->first()
                )?->created_at?->format('d M Y') ?? '—'
            )
                ->description('Most recent'),
        ];
    }
}

<?php

namespace App\Filament\Resources\PurchaseOrderResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\PurchaseOrder;


class PurchaseOrderStats extends StatsOverviewWidget
{
    public ?string $month = null;
    public ?string $from_date = null;
    public ?string $to_date = null;

    protected static bool $isLazy = false;

    public function updateFilters(array $filters): void
    {
        $this->month = $filters['month'] ?? null;
        $this->from_date = $filters['from_date'] ?? null;
        $this->to_date = $filters['to_date'] ?? null;
    }

    protected function getCards(): array
    {
        $query = PurchaseOrder::query();

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        $totalAmount = $query->sum('net_amount');
        $totalOrders = $query->count();

        return [
            Stat::make('Total PO Amount', number_format($totalAmount, 2))
                ->extraAttributes([ 'class' => 'po-total-stat']),

            Stat::make('Total Orders', $totalOrders)
                ->extraAttributes(['class' => 'bg-green-50 dark:bg-green-900/30']),
        ];
    }
}

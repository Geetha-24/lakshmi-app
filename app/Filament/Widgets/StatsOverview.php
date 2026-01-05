<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\salesOrderDetails;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        $todaySales = SalesOrder::whereDate('created_at', $today)->where('status','confirmed');
        // $SalesItemCount = $todaySales->with(['salesOrderDetails'=> function($q){
        //    $q->withoutTrashed()->sum('quantity');
        // }]);

        $totalQty = $todaySales
        ->withSum('salesOrderDetails', 'quantity')
        ->get()
        ->sum('sales_order_details_sum_quantity');
        return [
            Stat::make(
                'Today Sales Items',
                $totalQty
            )
            ->icon('heroicon-o-shopping-cart')
            ->color('success'),

            Stat::make(
                'Cash Sales',
                number_format(
                    SalesOrder::whereDate('created_at', $today)->where('status','confirmed')->whereHas('payments',function($q){
                        $q->where('payment_mode_id',1);
                    })->with('payments')->sum('total_amount'),
                    2
                )
            )
            ->icon('heroicon-o-banknotes')
            ->color('success'),

            Stat::make(
                'Credit Sales',
                number_format(
                     SalesOrder::whereDate('created_at', $today)
                    ->where(function ($q) {
                        $q->where('payment_status', 'UNPAID')
                        ->orWhereHas('payments', function ($p) {
                            $p->where('payment_mode_id', 2); // Credit mode
                        });
                    })
                    ->sum('total_amount'),
                    2
                )
            )
            ->icon('heroicon-o-credit-card')
            ->color('warning'),

            Stat::make(
                'Today Purchase Amount',
                number_format(
                    PurchaseOrder::whereDate('created_at', $today)
                        ->sum('gross_amount'),
                    2
                )
            )
            ->icon('heroicon-o-arrow-down-tray')
            ->color('danger'),
        ];
    }
}

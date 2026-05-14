<?php
namespace App\Services;

use App\Models\PurchaseBatch;
use App\Models\SaleBatchAllocation;
use App\Models\SalesOrder;
use App\Models\salesOrderDetails;
use Illuminate\Validation\ValidationException;

class SalesStockService
{
    public static function consumeStock(
        SalesOrder $sale,
        salesOrderDetails $item
    ): float {
        $remainingQty = $item->quantity;
        $lineProfit = 0;

        $batches = PurchaseBatch::where('pv_id', $item->pv_id)
            ->whereRaw('(total_stock_in - sold_qty) > 0')
            ->orderBy('created_at') // FIFO
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {

            if ($remainingQty <= 0) break;

            $available = ($batch->total_stock_in - $batch->sold_qty)+$batch->returned_qty;
            $consume   = min($available, $remainingQty);

            $profit =
                ($consume * $item->sold_price)
                - ($consume * $batch->purchase_price);

            // Deduct stock
            $batch->increment('sold_qty', $consume);

            SaleBatchAllocation::create([
                'so_id'        => $sale->id,
                'so_detail_id' => $item->id,
                'pb_id'        => $batch->id,
                'qty'          => $consume,
                'purchase_price' => $batch->purchase_price,
                'selling_price'  => $item->sold_price,
                'profit'         => $profit,
            ]);

            $lineProfit += $profit;
            $remainingQty -= $consume;
        }

        if ($remainingQty > 0) {
            throw ValidationException::withMessages([
                'stock' => "Insufficient stock for product {$item->pv_id}"
            ]);
        }

        return $lineProfit;
    }
}

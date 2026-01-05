<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\PurchaseBatch;
use App\Models\SaleBatchAllocation;
use App\Models\SalesOrder;
use App\Models\salesOrderDetails;
use Illuminate\Support\Facades\DB;
use App\Services\SalesPricingService;
use Illuminate\Validation\ValidationException;
use App\Services\LedgerService;



class SalesOrderService
{
    //public static function getSellingPrice($variantId)
    // {
    //     return PurchaseBatch::where('pv_id', $variantId)
    //     ->selectRaw('SUM(total_stock_in - sold_qty) >0 as stock')->max('selling_price')
    //         ?? 0;
    // }

    // public static function getAvailableStock($variantId)
    // {
    //     return PurchaseBatch::where('pv_id', $variantId)
    //         ->selectRaw('SUM(total_stock_in - sold_qty) as stock,max(selling_price)')
    //         ->value('stock') ?? 0;
    // }

    public static function getStockAndSellingPrice(int $variantId): array
    {
        $result = PurchaseBatch::where('pv_id', $variantId)
            ->whereRaw('(total_stock_in - sold_qty) > 0')
            ->selectRaw('
                SUM(total_stock_in - sold_qty) as stock,
                MAX(selling_price) as selling_price
            ')
            ->first();

        return [
            'avl_stock' => (int) ($result->stock ?? 0),
            'selling_price' => (float) ($result->selling_price ?? 0),
        ];
    }

    // public static function processSale(SalesOrder $sale, array $items)
    // {
    //     DB::transaction(function () use ($sale, $items) {

    //         $saleTotal = 0;
    //         $saleProfit = 0;

    //         foreach ($items as $item) {

    //             $saleItem = salesOrderDetails::create([
    //                 'so_id' => $sale->id,
    //                 'pv_id' => $item['pv_id'],
    //                 'quantity' => $item['quantity'],
    //                 'sold_price' => $item['sold_price'],
    //                 'line_total' => $item['quantity'] * $item['sold_price'],
    //             ]);

    //             $remainingQty = $item['quantity'];
    //             $lineProfit = 0;

    //             $batches = PurchaseBatch::where('pv_id', $item['pv_id'])
    //                 ->whereRaw('(total_stock_in - sold_qty) > 0')
    //                 ->orderBy('created_at') // FIFO
    //                 ->lockForUpdate()
    //                 ->get();

    //             foreach ($batches as $batch) {

    //                 if ($remainingQty <= 0) break;

    //                 $available = $batch->total_stock_in - $batch->sold_qty;
    //                 $consume = min($available, $remainingQty);

    //                 $profit =
    //                     ($consume * $item['sold_price'])
    //                     - ($consume * $batch->purchase_price);

    //                 $batch->increment('sold_qty', $consume);

    //                 SaleBatchAllocation::create([
    //                     'so_id' => $sale->id,
    //                     'so_detail_id' => $saleItem->id,
    //                     'pb_id' => $batch->id,
    //                     'qty' => $consume,
    //                     'purchase_price' => $batch->purchase_price,
    //                     'selling_price' => $item['sold_price'],
    //                     'profit' => $profit,
                        
    //                 ]);

    //                 $lineProfit += $profit;
    //                 $remainingQty -= $consume;
    //             }

    //             $saleItem->update([
    //                 'line_profit' => $lineProfit
    //             ]);

    //             $saleTotal += $saleItem->line_total;
    //             $saleProfit += $lineProfit;
    //         }

    //         $sale->update([
    //             'subtotal'     =>$saleTotal,
    //             'total_amount' => $saleTotal,
    //             'total_profit' => $saleProfit
    //         ]);
    //     });
    // }



    //New Code 

    /* ===========================
     * CREATE DRAFT
     * =========================== */
    public static function createDraft(array $data): SalesOrder
    {
        return DB::transaction(function () use ($data) {

            $sale = SalesOrder::create([
                'so_number'   => $data['so_number'],
                'customer_id'=> $data['customer_id'],
                'order_date' => $data['order_date'],
                'status'     => 'draft',
            ]);

            self::syncDraftItems($sale, $data['salesOrderDetails']);

            return $sale;
        });
    }

    /* ===========================
     * UPDATE DRAFT (EDIT FLOW)
     * =========================== */
    public static function updateDraft(SalesOrder $sale, array $data): SalesOrder
    {
        if ($sale->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Only draft orders can be edited'
            ]);
        }
        return DB::transaction(function () use ($sale, $data) {

            // 🔥 IMPORTANT: delete old items (no stock impact)
            $sale->salesOrderDetails()->delete();
            logger()->info(($data['salesOrderDetails']));

            self::syncDraftItems($sale, $data['salesOrderDetails']);

            return $sale;
        });
    }

    /* ===========================
     * CONFIRM ORDER (STOCK MOVES)
     * =========================== */
    public static function confirm(SalesOrder $sale): SalesOrder
    {
        if ($sale->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Only draft orders can be confirmed'
            ]);
        }

        return DB::transaction(function () use ($sale) {

            $saleTotal  = 0;
            $saleProfit = 0;
            $saleSubTotal = 0;
            $saleTax =0;

            foreach ($sale->salesOrderDetails as $item) {

                // 🔐 Stock + Profit happens ONLY here
                $lineProfit = SalesStockService::consumeStock($sale, $item);

                $item->update([
                    'line_profit' => $lineProfit
                ]);

                $taxRate = (float) 5; // eg: 18
                $lineTotal = (float) $item['line_total'];   // tax-inclusive amount

                $baseAmount = round(
                    $lineTotal / (1 + ($taxRate / 100)),
                    2
                );

                $taxAmount = round(
                    $lineTotal - $baseAmount,
                    2
                );
                $saleSubTotal +=$baseAmount;
                $saleTax    +=$taxAmount;
                $saleTotal  += $item->line_total;
                $saleProfit += $lineProfit;
            }



            $sale->update([
                'status'        => 'confirmed',
                'subtotal'      => $saleSubTotal,
                'tax_amount'    => $saleTax,
                'total_amount'  => $saleTotal,
                'total_profit'  => $saleProfit,
                'payment_status' => 'UNPAID',
                'paid_amount' => 0,
                'balance_amount' => $saleTotal,
            ]);
            LedgerService::debit(
            $sale->customer_id,
            'sales_order',
            $sale->id,
            $sale->total_amount
        );

            return $sale;
        });
    }

    /* ===========================
     * INTERNAL: SAVE DRAFT ITEMS
     * =========================== */
    private static function syncDraftItems(
        SalesOrder $sale,
        array $items
    ): void {
        logger()->info([
            "sync data"=>$items
        ]);
        foreach ($items as $item) {

            salesOrderDetails::create([
                'so_id'      => $sale->id,
                'pv_id'      => $item['pv_id'],
                'quantity'   => $item['quantity'],
                'sold_price' => $item['sold_price'],
                'line_total' => SalesPricingService::lineTotal(
                    $item['quantity'],
                    $item['sold_price']
                ),
            ]);
        }
 
    }
}

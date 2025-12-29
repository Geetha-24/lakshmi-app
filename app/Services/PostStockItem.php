<?php

namespace App\Services;

use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseBatch;
use App\Models\PurchaseOrder;
use App\Models\PurchaseStockPosting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Support\BatchCodeGenerator;



class PostStockItem
{
    public function handle(PurchaseOrderDetail $detail, int $qty): void
    {
        if ($qty > ($detail->qunatity - $detail->posted_qty)) {
            throw ValidationException::withMessages([
                'qty' => 'Posting quantity exceeds remaining quantity.',
            ]);
        }
        if($detail->batch_code == null)
        {
         $batchCode = BatchCodeGenerator::make($detail);

        }else{
            $batchCode = $detail->batch_code;
        }


        DB::transaction(function () use ($detail, $qty,$batchCode) {
             $detail = PurchaseOrderDetail::where('id', $detail->id)
                ->lockForUpdate()
                ->first();

            $available = $detail->qunatity - $detail->posted_qty;
            $newPostedQty = $detail->posted_qty+$qty;
            $remaining = $detail->qunatity - $newPostedQty;
            $purchaseDate = PurchaseOrder::where('id',$detail->po_id)->value('invoice_date');
            logger()->info('date',[
                'Date' => $purchaseDate,
                'av' => $available,
                're' => $remaining
            ]);

            if ($qty > $available) {
               throw ValidationException::withMessages([
                     'qty' => "Only {$available} quantity remaining.",
                ]);
            }

            /* Create Purchase Batch*/

            $isBatched = PurchaseBatch::where('po_detail_id',$detail->id)->exists();
            if($isBatched)
            {
                $batchData = PurchaseBatch::where('po_detail_id',$detail->id)->first();
                $batchData->increment('total_stock_in',$qty);
                    
            }else{
                PurchaseBatch::create([
                'po_id' => $detail->po_id,
                'po_detail_id'=>$detail->id,
                'pv_id'=>$detail->pv_id,
                'batch_code'=>$batchCode,
                'purchased_quantity'=>$detail->qunatity,
                'total_stock_in'=>$qty,
                'purchase_price'=>$detail->SP_without_profit,
                'selling_price'=>$detail->SP_with_profit,
                'purchase_date'=>$purchaseDate
            ]);
            }

            

            /* Create purchase batch */
            // PurchaseBatch::create([
            //     'product_variant_id' => $detail->pv_id,
            //     'purchase_price' => $detail->unit_price,
            //     'selling_price' => $detail->landed_cost_per_unit, // or calculated
            //     'quantity' => $qty,
            //     'remaining_quantity' => $remaining,
            //     'purchase_date' => $purchaseDate,
            //     'batch_code' => $batchCode
            // ]);

            //*Create Stock Posting */

           
            PurchaseStockPosting::create([
                'po_detail_id'=>$detail->id,
                'batch_code'=>$batchCode,
                'quantity'=>$detail->qunatity,
                'posted_qty' => $qty,
                'remaining_qty' => $remaining,
                'posting_date'=> now()
            ]);

            /* Update product stock */
            $variant = $detail->productVariant;
            $variant->increment('stock', $qty);

            /* Update detail row */
            $detail->increment('posted_qty', $qty);

            if ($detail->posted_qty + $qty >= $detail->qunatity) {
                $detail->update([
                    'is_posted' => 1,
                    'posted_at' => now(),
                ]);
            }
        });
    }
}

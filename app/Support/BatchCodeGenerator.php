<?php

namespace App\Support;

use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrder;

class BatchCodeGenerator
{
    public static function make(PurchaseOrderDetail $poDetail): string
    {
        // Reuse if already exists
        if ($poDetail->batch_code) {
            return $poDetail->batch_code;
        }
         $purchaseDate = PurchaseOrder::where('id',$poDetail->po_id)->value('invoice_date');

        $date =date('dmY', strtotime($purchaseDate));


        $batchCode = "PB-{$date}-{$poDetail->id}";

        // Persist once
        $poDetail->update([
            'batch_code' => $batchCode,
        ]);

        return $batchCode;
    }
}

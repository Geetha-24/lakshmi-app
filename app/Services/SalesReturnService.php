<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\{
    CustomersLedger,
    SalesReturn,
    SalesReturnItem,
    SalesReturnBatchAllocation,
    SalesOrder,
    SalesOrderDetail,
    SalesBatchAllocation,
    PurchaseBatch,
    Ledger,
    SaleBatchAllocation,
    salesOrderDetails
};
use Illuminate\Validation\ValidationException;

class SalesReturnService
{
    public static function createDraft($data): SalesReturn
    {
        //$so = SalesOrder::findOrFail($soId);
        logger()->info($data);
        $sr = SalesReturn::create([
            'so_id' => $data['so_id'],
            'customer_id' => $data['customer_id'],
            'return_no' =>$data['return_no'],
            'return_date' => now(),
            'status' => 'draft',
            'total_amount' => $data['total_amount'],
        ]);
         self::syncDraftItems($sr, $data['items']);

            return $sr;
    }

    private static  function syncDraftItems(SalesReturn $sr, array $items): void
    {

        $sr->items()->delete();

        foreach ($items as $item) {
            if (($item['return_qty'] ?? 0) <= 0) {
                continue;
            }

            SalesReturnItem::create([
                'sr_id' => $sr->id,
                'so_detail_id' => $item['so_detail_id'],
                'pv_id' => $item['pv_id'],
                'return_qty' => $item['return_qty'],
                'rate' => $item['rate'],
                //'line_total' => $item['return_qty'] * $item['rate'],
            ]);
        }

        // $sr->update([
        //     'total_amount' =>,
        // ]);
    }

    public static  function updateDraft(SalesReturn $sr, array $data): SalesReturn
    {
        abort_if($sr->status !== 'draft', 403);

        $sr->update([
            'return_date' => $data['return_date'],
        ]);

        self::syncDraftItems($sr, $data['items']);

        return $sr;
    }

    public static function confirmSalesReturn(SalesReturn $data): SalesReturn
    {
        return DB::transaction(function () use ($data) {

            $sr = SalesReturn::where('so_id', $data['so_id'])
                ->where('status', 'draft')
                ->lockForUpdate()
                ->first();

            if (!$sr) {
                $sr = self::createDraft($data['so_id']);
            }

            self::syncDraftItems($sr, $data['items']->toArray());

            self::validateReturnQty($sr);

            self::adjustStock($sr);

            self::createSalesReturnBatchAllocation($sr);

            self::adjustCustomerLedger($sr);

            self::adjustPaymentIfNeeded($sr);

            $sr->update(['status' => 'confirmed']);

            return $sr;
        });
    }

    private static function validateReturnQty(SalesReturn $sr): void
    {
        foreach ($sr->items as $item) {
            $soDetail = salesOrderDetails::lockForUpdate()
                ->findOrFail($item->so_detail_id);

            $allowed = $soDetail->quantity - $soDetail->returned_qty;

            if ($item->return_qty > $allowed) {
                throw ValidationException::withMessages([
                    'return_qty' => 'Return quantity exceeds allowed limit',
                ]);
            }
        }
    }

    private static function adjustStock(SalesReturn $sr): void
    {
        foreach ($sr->items as $item) {

            $qty = $item->return_qty;

            $allocations = SaleBatchAllocation::where(
                'so_detail_id',
                $item->so_detail_id
            )->orderByDesc('id')->get();

            foreach ($allocations as $alloc) {
                if ($qty <= 0) break;

                $returnQty = min($alloc->qty, $qty);

                PurchaseBatch::where('id', $alloc->pb_id)
                    ->increment('returned_qty', $returnQty);

                $qty -= $returnQty;
            }

            salesOrderDetails::where('id', $item->so_detail_id)
                ->increment('returned_qty', $item->return_qty);
        }
    }

    private static function createSalesReturnBatchAllocation(SalesReturn $sr): void
    {
        foreach ($sr->items as $item) {

            $qty = $item->return_qty;

            $allocations = SaleBatchAllocation::where(
                'so_detail_id',
                $item->so_detail_id
            )->orderByDesc('id')->get();

            foreach ($allocations as $alloc) {
                if ($qty <= 0) break;

                $usedQty = min($alloc->qty, $qty);

                SalesReturnBatchAllocation::create([
                    'sr_id' => $sr->id,
                    'sales_return_item_id' => $item->id,
                    'pb_id' => $alloc->pb_id,
                    'qty_in' => $usedQty,
                ]);

                $qty -= $usedQty;
            }
        }
    }

    private static function adjustCustomerLedger(SalesReturn $sr): void
    {
         LedgerService::debit(
            $sr->customer_id,
            'SALES RETURN',
            $sr->id,
            $sr->total_amount);
    }

    private static function adjustPaymentIfNeeded(SalesReturn $sr): void
{
    $so = SalesOrder::find($sr->so_id);

    if ($so->payment_status === 'UNPAID') {
        return;
    }

    // Paid sale → customer advance created
    // Refund handled via separate Refund module (recommended)
}

            





}
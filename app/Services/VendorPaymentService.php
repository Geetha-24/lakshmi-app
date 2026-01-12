<?php

namespace App\Services;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\VendorPaymentSettlement;
use App\Models\VendorLedger;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;

class VendorPaymentService
{
    public static function makePayment(Vendor $vendor, array $data)
    {
        return DB::transaction(function () use ($vendor, $data) {

            $payment = VendorPayment::create([
                'vendor_id' => $vendor->id,
                'payment_date' => $data['payment_date'],
                'payment_mode' => $data['payment_mode'],
                'amount' => $data['amount'],
                'unallocated_amount' => $data['amount'],
                'cheque_no' => $data['cheque_no'] ?? null,
            ]);

            // Ledger entry
            VendorLedger::create([
                'vendor_id' => $vendor->id,
                'date' => $data['payment_date'],
                'reference_type' => 'vendor_payment',
                'reference_id' => $payment->id,
                'debit' => $data['amount'],
            ]);

            if ($vendor->payment_settlement_type == 1) {
                self::autoSettleBills($vendor, $payment);
            }

            return $payment;
        });
    }

    protected static function autoSettleBills(Vendor $vendor, VendorPayment $payment)
    {
        $remaining = $payment->amount;

        $bills = PurchaseOrder::where('vendor_id', $vendor->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('invoice_date')
            ->lockForUpdate()
            ->get();

        foreach ($bills as $bill) {
            if ($remaining <= 0) break;

            $settle = min($bill->due_amount, $remaining);

            VendorPaymentSettlement::create([
                'vendor_payment_id' => $payment->id,
                'po_id' => $bill->id,
                'settled_amount' => $settle,
            ]);

            $bill->paid_amount += $settle;
            $bill->due_amount -= $settle;
            $bill->payment_status = $bill->due_amount == 0 ? 'paid' : 'partial';
            $bill->save();

            $remaining -= $settle;
        }

        $payment->allocated_amount = $payment->amount - $remaining;
        $payment->unallocated_amount = $remaining;
        $payment->save();
    }
}

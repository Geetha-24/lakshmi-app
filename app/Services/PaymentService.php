<?php

namespace App\Services;

use App\Models\Payment;
use App\Services\LedgerService;
use App\Models\SalesOrder;
use App\Models\PaymentAllocation;


class PaymentService
{
    public static function makePayment(
    $salesOrder,
    float $amount,
    array $data
    )
    {
        $customerId=$data['c_id'];
        if(!empty($salesOrder))
        {
            $customerId = $salesOrder->customer_id;
        }
        
        
        // 1️⃣ Create payment
        $payment = Payment::create([
            'c_id'    => $customerId,
            'so_id' => $salesOrder?->so_id,
            'payment_date'   => now(),
            'amount'         => $amount,
            'payment_mode_id'   => $data['payment_mode_id'],
            'reference_no'   => $data['reference_no'] ?? null,
        ]);

        // 2️⃣ Update sales order amounts
        // if($salesOrder)
        // {

        // $salesOrder->paid_amount += $amount;
        // $salesOrder->balance_amount = 
        //     $salesOrder->total_amount - $salesOrder->paid_amount;

        // $salesOrder->payment_status =
        //     $salesOrder->balance_amount <= 0
        //         ? 'paid'
        //         : 'partial';

        // $salesOrder->save();
        // }

       
           if ($salesOrder) {
                // Single sales order payment
                self::applyToSingleOrder($salesOrder, $payment);
            } else {
                // 🔥 AUTO FIFO ALLOCATION
                self::allocateAcrossOrders($customerId, $payment);
            }

             // 3️⃣ Update customer ledger
            LedgerService::credit(
                $customerId,
                'payment',
                $payment->id,
                $amount
            );

        return $payment;
    }

    /**
     * SINGLE ORDER PAYMENT
     */
    protected static function applyToSingleOrder(
        SalesOrder $order,
        Payment $payment
    ): void {

        if ($payment->amount > $order->balance_amount) {
            throw new \Exception('Payment exceeds sales order balance');
        }

        // Allocation record
        PaymentAllocation::create([
            'payment_id'      => $payment->id,
            'so_id'           => $order->id,
            'allocated_amount'=> $payment->amount,
        ]);

        // Update order
        $order->paid_amount += $payment->amount;
        $order->balance_amount -= $payment->amount;
        $order->payment_status =
            $order->balance_amount <= 0 ? 'paid' : 'partial';

        $order->save();
    }

    /**
     * AUTO FIFO ALLOCATION (ON-ACCOUNT PAYMENT)
     */
    protected static function allocateAcrossOrders(
        int $customerId,
        Payment $payment
    ): void {

        $remaining = $payment->amount;

        // 🔒 Lock unpaid orders (ERP safety)
        $orders = SalesOrder::where('customer_id', $customerId)
            ->where('balance_amount', '>', 0)
            ->orderBy('created_at') // FIFO
            ->lockForUpdate()
            ->get();

        foreach ($orders as $order) {

            if ($remaining <= 0) {
                break;
            }

            $allocate = min($order->balance_amount, $remaining);

            // Allocation row
            PaymentAllocation::create([
                'payment_id'      => $payment->id,
                'so_id'           => $order->id,
                'allocated_amount'=> $allocate,
            ]);

            // Update order
            $order->paid_amount += $allocate;
            $order->balance_amount -= $allocate;
            $order->payment_status =
                $order->balance_amount <= 0 ? 'paid' : 'partial';

            $order->save();

            $remaining -= $allocate;
        }
    
}

}

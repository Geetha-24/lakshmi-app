<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\SalesOrder;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Notifications\Notification;


class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
            protected static bool $canCreateAnother = false;


   public function mount(): void
    {
        // Check if sales_order_id exists in URL query
        if ($salesOrderId = request()->get('so_id')) {
            $order = SalesOrder::with('customer')->find($salesOrderId);
            if ($order) {
            // Fill form fields
            $this->form->fill([
                'so_id'          => $order->id,
                'c_id'           => $order->customer_id,
                'payment_date'   => now()->format('Y-m-d'),
                'order_total'    =>  $order->total_amount,
                'order_paid'     => $order->paid_amount,
                'order_balance'  =>$order->balance_amount,
                'amount'         => $order->balance_amount, // optional: prefill full balance
            ]);
        }
    }
        if (request()->has('customer_id')) {
            $this->form->fill([
                'c_id'  => request('customer_id'),
                'payment_date' => now()->toDateString(),
                'payment_mode_id'=> '1',
            ]);
        }
    }

    protected function handleRecordCreation(array $data): Payment
    {
        logger()->info("enterpayment");
        
        $amount = (float) $data['amount'];

        // 3️⃣ Pass remaining data
        return PaymentService::makePayment(
        ($data['so_id'] !=null)
                ? SalesOrder::find($data['so_id'])
                : null,            
                $amount,
                $data
        );
    }

     protected function getRedirectUrl(): string
    {
        return $this->previousUrl ? $this->getResource()::getUrl('index') : $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification  
    {
        return Notification::make()
        ->success()
        ->title("Payment")
        ->body("The payment has been done");
    
    }
}

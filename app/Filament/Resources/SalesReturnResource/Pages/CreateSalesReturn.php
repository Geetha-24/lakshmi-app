<?php

namespace App\Filament\Resources\SalesReturnResource\Pages;

use App\Filament\Resources\SalesReturnResource;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Services\SalesReturnService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesReturn extends CreateRecord
{
    protected static string $resource = SalesReturnResource::class;
    protected static bool $canCreateAnother = false;


     public function mount(): void
    {
        parent::mount();

        $soId = request('so_id');
        abort_if(!$soId, 404);

        $so = SalesOrder::with([
            'Customer',
            'salesOrderdetails.ProductVariant'
        ])->findOrFail($soId);

        $this->form->fill([
            'so_id' => $so->id,
            'customer_id'=>$so->customer_id,
            'return_no' => 'SR-' . str_pad(SalesReturn::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'customer_name' => $so->Customer->name,
            'return_date' => now()->toDateString(),
            'total_amount'=>$so->total_amount,
            'items' => $so->salesOrderdetails->map(fn ($d) => [
                'so_detail_id' => $d->id,
                'pv_id' => $d->pv_id,
                'product_name' => $d->ProductVariant->p_name,
                'sold_qty' => $d->quantity,
                'already_returned_qty' => $d->returned_qty ?? 0,
                'return_qty' => 0,
                'rate' => $d->sold_price,
                //'amount' => $d->sold_price,

            ])->toArray(),

        ]);
    }

    public function getActions(): array
    {
        return[
           
         Action::make('confirm')
             ->label(fn ($record) =>
                $record?->status === 'confirmed'
                    ? 'CONFIRMED'
                    : 'CONFIRM ORDER'
            )
            ->icon('heroicon-o-check-circle')
            ->color(fn ($record) =>
                $record?->status === 'confirmed'
                    ? 'success'
                    : 'primary'
            )
            ->disabled(fn ($record) =>
                $record?->status === 'confirmed'
            )
             ->requiresConfirmation()
             ->action(function ($data, $record = null) {
                $data = $this->form->getState();
                $record = $this->record;
             if (! $record) {
                 $sale = SalesReturnService::createDraft($this->record);
             } else {
                 $sale = $record;
             }
             SalesReturnService::confirmSalesReturn($sale);
             Notification::make()
            ->title('Sales Return Confirmed')
            ->body('Your order returned successfully')
            ->success()
            //->icon('heroicon-o-badge-check')
            ->send();
         }),
        ];
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return SalesReturnService::createDraft($data);
    }

}

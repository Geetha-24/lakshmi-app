<?php

namespace App\Filament\Resources\SalesReturnResource\Pages;

use App\Filament\Resources\SalesReturnResource;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Services\SalesReturnService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditSalesReturn extends EditRecord
{
    protected static string $resource = SalesReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
                logger()->info($record);
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

     public function mutateFormDataBeforeFill(array $data):array
     {

       $data['so_id'] = $this->record->so_id;
       $data['customer_id'] = $this->record->customer_id;
       $data['total_amount'] = $this->record->total_amount;
       $data['customer_name'] = Customer::where('id',$this->record->customer_id)->pluck('name');
       $salesReturnData = $this->record->items;
       foreach($salesReturnData as $returnData)
        {
            $returnData['product_name'] = $returnData->productVariant->p_name;
            $returnData['sold_qty']=$returnData->salesOrderItem->quantity;
            $returnData['already_returned_qty']=$returnData->salesOrderItem->returned_qty;

        }
       $data['items']= $salesReturnData;

        return $data;

     }

     protected function handleRecordUpdate(Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return SalesReturnService::updateDraft($record, $data);
    }
}

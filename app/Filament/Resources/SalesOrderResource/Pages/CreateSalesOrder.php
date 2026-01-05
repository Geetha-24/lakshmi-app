<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Services\SalesOrderService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;
        protected static bool $canCreateAnother = false;


    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
            return SalesOrderService::createDraft($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Sales Order Saved')
            ->body('Sales order has been saved as draft.')
            ->success()
            ->icon('heroicon-o-document-text');
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
                logger()->info("on confirm salesorder",["data"=>$data,"rec"=>$record]);
             if (! $record) {
                 $sale = SalesOrderService::createDraft($data);
             } else {
                 $sale = $record;
             }
             SalesOrderService::confirm($sale);
             Notification::make()
            ->title('Sales Order Confirmed')
            ->body('Sales order has been confirmed and is ready for payment.')
            ->success()
            //->icon('heroicon-o-badge-check')
            ->send();
         }),
        ];
    }
}

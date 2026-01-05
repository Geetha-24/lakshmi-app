<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\SalesOrderService;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;



class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }
    protected function handleRecordUpdate(Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return SalesOrderService::updateDraft($record, $data);
    }

     public function getHeaderActions(): array
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
                    ->icon('heroicon-o-badge-check')
                    ->send();
                })

          
        ];
    }
}

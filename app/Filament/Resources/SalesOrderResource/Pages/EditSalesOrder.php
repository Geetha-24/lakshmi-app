<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\SalesOrderService;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;



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
                ->label('Confirm')
                ->color('success')
                ->requiresConfirmation()
                ->action(function ($data, $record = null) {
                    if (! $record) {
                        $sale = SalesOrderService::createDraft($data);
                    } else {
                        $sale = $record;
                    }
                    SalesOrderService::confirm($sale);
                })

          
        ];
    }
}

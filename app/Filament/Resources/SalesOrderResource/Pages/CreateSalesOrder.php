<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Services\SalesOrderService;
use Filament\Actions\Action;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;
        protected static bool $canCreateAnother = false;


    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
            return SalesOrderService::createDraft($data);
    }

     public function getActions(): array
    {
        return[
           
         Action::make('confirm')
             ->label('Confirm')
             ->color('success')
             ->icon('heroicon-o-check')
             ->requiresConfirmation()
             ->action(function ($data, $record = null) {
             if (! $record) {
                 $sale = SalesOrderService::createDraft($data);
             } else {
                 $sale = $record;
             }
             SalesOrderService::confirm($sale);
         }),
        ];
    }
}

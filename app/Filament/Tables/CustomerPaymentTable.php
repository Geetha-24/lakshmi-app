<?php

namespace App\Filament\Tables;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;

trait CustomerPaymentTable
{
                protected static bool $isLazy = false;

    public function customerPaymentTable(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('salesOrder.so_number')
                    ->label('Order No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('INR'),

                Tables\Columns\TextColumn::make('paymentModeMaster.mode')
                    ->label('Mode'),

                Tables\Columns\TextColumn::make('created_at')
                    ->date(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

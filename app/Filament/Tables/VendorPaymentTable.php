<?php

namespace App\Filament\Tables;

use App\Models\VendorPayment;
use Filament\Tables;
use Filament\Tables\Table;

trait VendorPaymentTable
{

            protected static bool $isLazy = false;
 
    public function vendorPaymentTable(Table $table): Table
    {
        return $table
            ->query(
                VendorPayment::query()
            )
            ->columns([
            Tables\Columns\TextColumn::make('vendorPaySettlement.purchaseInvoice.invoice_number')->label('Bill No'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->date(),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('INR'),

                Tables\Columns\TextColumn::make('paymentModeMaster.mode')
                    ->label('Mode'),

            ])
            ->defaultSort('payment_date', 'desc');
    }
}

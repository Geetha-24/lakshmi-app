<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentSettlements';
            protected static bool $isLazy = false;



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('vendorPayments')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('vendor_id')
            ->columns([
                Tables\Columns\TextColumn::make('purchaseInvoice.invoice_number')->label('Bill No'),

                Tables\Columns\TextColumn::make('vendorPay.payment_date')->label('Paid At')
                    ->date(),

                Tables\Columns\TextColumn::make('vendorPay.paymentModeMaster.mode')->label('Payment Mode'),

                Tables\Columns\TextColumn::make('settled_amount')->label('Amount')
                    ->money('INR'),

                // Tables\Columns\TextColumn::make('allocated_amount')
                //     ->money('INR'),

                // Tables\Columns\TextColumn::make('unallocated_amount')
                //     ->money('INR')
                //     ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),
            ])
            ->defaultSort('payment_date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

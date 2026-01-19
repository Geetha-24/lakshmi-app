<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'purchases';
            protected static bool $isLazy = false;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('purchaseOrders')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoice_date')->label('Invoice Date')
                    ->date(),

                Tables\Columns\TextColumn::make('net_amount')->label('Bill Amount')
                    ->money('INR'),

                Tables\Columns\TextColumn::make('due_amount')->label('Balance')
                    ->money('INR')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('payment_status')
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'success' => 'paid',
                    ])
            ])
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

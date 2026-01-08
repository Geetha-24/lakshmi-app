<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesOrderRelationManager extends RelationManager
{
    protected static string $relationship = 'SalesOrder';
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer_id')
            ->columns([
                Tables\Columns\TextColumn::make('so_number')->label('Order No'),
                Tables\Columns\TextColumn::make('created_at')->date(),
                Tables\Columns\TextColumn::make('total_amount')->money('INR'),
                Tables\Columns\TextColumn::make('paid_amount')->money('INR'),
                Tables\Columns\TextColumn::make('balance_amount')->money('INR'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'paid' ? 'success' : 'danger'
                    ),
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

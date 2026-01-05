<?php

namespace App\Filament\Resources\StockResource\RelationManagers;

use App\Models\PurchaseBatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseBatchRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseBatch';
    protected static ?string $title = 'Purchase Batches';
    protected static ?string $label = 'PurchaseBatch';



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pv_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            //->recordTitleAttribute('pv_id')
            ->columns([
                Tables\Columns\TextColumn::make('batch_code'),
                Tables\Columns\TextColumn::make('purchase_date'),
                Tables\Columns\TextColumn::make('purchased_quantity'),
                Tables\Columns\TextColumn::make('total_stock_in'),
                Tables\Columns\TextColumn::make('sold_qty'),
                Tables\Columns\TextColumn::make('purchase_price'),
                Tables\Columns\TextColumn::make('selling_price'),




            ])
            ->filters([
                //
            ])
           
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //     Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}

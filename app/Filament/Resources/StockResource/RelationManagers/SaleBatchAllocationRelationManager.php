<?php

namespace App\Filament\Resources\StockResource\RelationManagers;

use App\Models\SaleBatchAllocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleBatchAllocationRelationManager extends RelationManager
{
    protected static string $relationship = 'salesOrderDetails';
    protected static ?string $title = 'Sales (Batch-wise)';

     /* -----------------------------
     |  READ ONLY CONFIG
     |-----------------------------*/
    protected function canCreate(): bool { return false; }
    protected function canEdit($record): bool { return false; }
    protected function canDelete($record): bool { return false; }
    protected function canDeleteAny(): bool { return false; }


     /**
     * Custom query joining multiple tables
     */
    protected function getTableQuery(): Builder
    {
        return SaleBatchAllocation::query()
            ->select([
                'sales_batch_allocation.id',
                'sales_batch_allocation.qty',
                'sales_batch_allocation.selling_price',
                'sales_batch_allocation.created_at',

                'sales_orders.order_date',
                'purchase_batches.batch_code',
            ])
            ->join(
                'so_detail',
                'so_detail.id',
                '=',
                'sales_batch_allocation.so_detail_id'
            )
            ->join(
                'sales_orders',
                'sales_orders.id',
                '=',
                'so_detail.so_id'
            )
            ->join(
                'purchase_batches',
                'purchase_batches.id',
                '=',
                'sales_batch_allocation.pb_id'
            )
            ->where(
                'so_detail.pv_id',
                $this->ownerRecord->id
            );
    }

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
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('order_date')
                    ->label('Sales Date')
                    ->date(),

                Tables\Columns\TextColumn::make('batch_code')
                    ->label('Batch Code'),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Quantity'),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->money('INR'),
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

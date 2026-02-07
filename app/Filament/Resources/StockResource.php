<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\FiltersLayout;



class StockResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

      protected static ?string $navigationGroup = 'Stock Management';
    protected static ?string $navigationLabel = 'Stock';

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()

        // Purchased Qty
        ->withSum([
            'purchaseBatch as purchased_stock' => fn ($q) =>
                $q->where('status', 0)->whereNull('deleted_at')
        ], 'purchased_quantity')

        // Posted Stock
        ->withSum([
            'purchaseBatch as posted_stock' => fn ($q) =>
                $q->where('status', 0)->whereNull('deleted_at')
        ], 'total_stock_in')

        // Sold Stock
        ->withSum([
            'purchaseBatch as sold_stock' => fn ($q) =>
                $q->where('status', 0)->whereNull('deleted_at')
        ], 'sold_qty')

        // Available Stock (IN - OUT)
        ->selectSub(function ($q) {
            $q->from('purchase_batches')
                ->selectRaw('
                    COALESCE(SUM(total_stock_in - sold_qty), 0)
                ')
                ->whereColumn(
                    'purchase_batches.pv_id',
                    'product_variants.id'
                )
                ->where('status', 0)
                ->whereNull('deleted_at')
                ->whereColumn('total_stock_in', '>', 'sold_qty');
        }, 'available_stock')

        // Unposted Stock
        ->selectSub(function ($q) {
            $q->from('purchase_batches')
                ->selectRaw('
                    COALESCE(SUM(purchased_quantity - total_stock_in), 0)
                ')
                ->whereColumn(
                    'purchase_batches.pv_id',
                    'product_variants.id'
                )
                ->where('status', 0)
                ->whereNull('deleted_at')
                ->whereColumn('purchased_quantity', '!=', 'total_stock_in');
        }, 'unposted_stock');
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('p_name')->label('Product Name')->searchable()
                ->url(fn ($record) =>
                    route(
                        'filament.admin.resources.product-variants.edit',
                        $record
                    )
                )
                ->color('primary'),

                //  Tables\Columns\TextColumn::make('purchased_stock')->label('Purchased')
                // ->getStateUsing(function ($record){
                //     $pur_qty = $record->purchaseBatch()->where('status',0)->where('deleted_at','=',null)
                //     ->sum('purchased_quantity');

                //     return $pur_qty;
                // }),

                // Tables\Columns\TextColumn::make('posted_stock')->label('Posted Stock')
                // ->getStateUsing(function ($record){
                //     $stock_posted = $record->purchaseBatch()->where('status',0)->where('deleted_at','=',null)
                //     ->sum('total_stock_in');

                //     return $stock_posted;
                // }),


                // Tables\Columns\TextColumn::make('unposted_stock')->label('Unposted Stock')
                // ->getStateUsing(function ($record)
                // {
                //      $stock_posted = $record->purchaseBatch()->where('status',0)->where('deleted_at','=',null)
                //     ->whereColumn('total_stock_in','!=','purchased_quantity')->sum('total_stock_in');

                //     $purchased_qty = $record->purchaseBatch()->where('status',0)->where('deleted_at','=',null)
                //     ->whereColumn('total_stock_in','!=','purchased_quantity')->sum('purchased_quantity');


                //     return $purchased_qty - $stock_posted;
                // }),

                // Tables\Columns\TextColumn::make('available_stock')->label('Posted Stock In hand')
                // ->getStateUsing(function ($record){
                //     $stock_in = $record->purchaseBatch()->where('status',0)->where('deleted_at','=',null)
                //     ->whereColumn('total_stock_in','>','sold_qty')->sum('total_stock_in');

                //     $stock_out =  $record->purchaseBatch()->where('status',0)->where('deleted_at','=',null)
                //     ->whereColumn('total_stock_in','>','sold_qty')->sum('sold_qty');

                //     return $stock_in - $stock_out;
                // }),
                //  Tables\Columns\TextColumn::make('Sold_stock')->label('Sold Stock')
                // ->getStateUsing(function ($record){
                //     $sold_stock = $record->purchaseBatch()->where('status',0)->where('deleted_at','=',null)
                //     ->sum('sold_qty');

                //     return $sold_stock;
                // })

                Tables\Columns\TextColumn::make('purchased_stock')
                ->label('Purchased')->default(0),

                Tables\Columns\TextColumn::make('posted_stock')
                    ->label('Posted Stock')->default(0),

                Tables\Columns\TextColumn::make('unposted_stock')
                    ->label('Unposted Stock'),

                Tables\Columns\TextColumn::make('available_stock')
                    ->label('Posted Stock In Hand'),

                Tables\Columns\TextColumn::make('sold_stock')
                    ->label('Sold Stock')->default(0)->sortable()



            ])
           ->filters([
           
            SelectFilter::make('sort_by')
                ->label('Sort By')
                ->options([
                    'latest' => 'Latest',
                    'oldest' => 'Oldest',
                     'in' => 'In Stock',
                     'out' => 'Out of Stock',
                     'high_sales'=> 'High Sales',
                     'low_sales' => 'Low Sales',
                     'unpost' => 'Unpost Stock'

                ])
                ->query(fn (Builder $query) => $query), // 🔥 stop WHERE clause
                SelectFilter::make('category')
                ->label('Category')
                ->relationship('product.category','name')
                ->query(fn (Builder $query) => $query), // 🔥 stop WHERE clause
                SelectFilter::make('packing_type')
                ->label('packing Type')
                ->relationship('packing_type','name')
                ->query(fn (Builder $query) => $query),

            ])
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)

            ->modifyQueryUsing(function (Builder $query, $livewire) {

                $sortBy = data_get($livewire, 'tableFilters.sort_by.value');
                $category = data_get($livewire, 'tableFilters.category.value');
                $packing = data_get($livewire, 'tableFilters.packing_type.value');


                if ($sortBy === 'latest') {
                    $query->reorder('created_at', 'desc');
                }

                if ($sortBy === 'oldest') {
                    $query->reorder('created_at', 'asc');
                }
                if ($sortBy === 'in') {
                    $query->having('available_stock','>',0);
                }
                 if ($sortBy === 'out') {
                    $query->having('available_stock','<=',0);
                }
                if ($sortBy === 'high_sales') {
                    $query->having('sold_stock','>',0);
                }
                if ($sortBy === 'low_sales') {
                    $query->having('sold_stock','<',0);
                }
                if($sortBy === 'unpost')
                {
                    $query->having('unposted_stock','>',0);
                }
                if($category)
                {
                    $query->whereHas('product.category', function($q)use($category) {
                     $q->where('id', $category);
                });
                }
                if($packing)
                {
                    $query->whereHas('packing_type', function($q)use($packing) {
                     $q->where('id', $packing);
                });
                }
                logger()->info($query->toSql());
            })
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->label('Batch'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PurchaseBatchRelationManager::class,
            RelationManagers\SaleBatchAllocationRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
            'view' => Pages\EditStock::route('/{record}/batch'),

        ];
    }
}

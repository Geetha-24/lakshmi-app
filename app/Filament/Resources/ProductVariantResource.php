<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Filament\Resources\ProductVariantResource\RelationManagers;
use App\Models\Category;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Purchase';

    protected static ?string $navigationLabel = 'ProductVariant';


    public static function form(Form $form): Form
    {
        return $form
               ->schema([
                Forms\Components\TextInput::make('p_name')->disabled()
                ->dehydrated(true)
                ->unique(ignoreRecord:true)
                ->required(),
                Forms\Components\Select::make('product_id')->label('Product Name')
                    ->relationship('product','name')
                     ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::updateProductName($set, $get);
                     })
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                     ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::updateProductName($set, $get);
                     })
                    ->maxLength(255),
                Forms\Components\Select::make('unit_id')->label('Select Unit')
                    ->relationship('unit','name')
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::updateProductName($set, $get);
                     })
                    ->required(),
                Forms\Components\Select::make('packing_type_id')->label('Select Packing Type')
                    ->relationship('packing_type','name')
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::updateProductName($set, $get);
                     })
                    ->required(),
                Forms\Components\TextInput::make('purchase_price')->label('Last Purchase Price')
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('selling_price')
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->default(0),
                Forms\Components\Radio::make('status')
                ->options([
                    0 =>"Active",
                    1=>"InActive"
                ])->required()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('p_name')->label('Product')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('quantity')->label('Quantity')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('unit.name')->label('Unit')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('packing_type.name')->label('')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')->label('Purchase Price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')->label('Selling Price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')->label('Stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Active\InActive')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'Active' : 'Inactive'),
            ])
           ->filters([
            SelectFilter::make('sort_by')
                ->label('Sort By')
                ->options([
                    'latest' => 'Latest',
                    'p_name' => 'Product (A → Z)',
                    'h_stock'  => 'High Stock',
                    'l_stock'  =>  'Low Stock'

                ])
                ->query(fn (Builder $query) => $query), // 🔥 stop WHERE clause
        ])
        ->modifyQueryUsing(function (Builder $query, $livewire) {

            $sortBy = data_get($livewire, 'tableFilters.sort_by.value');
            logger()->info($query->toSql());
            if ($sortBy === 'latest') {
                $query->reorder('created_at', 'desc');
            }

            if ($sortBy === 'p_name') {
                $query->reorder('p_name', 'asc');
            }

             if ($sortBy === 'h_stock') {
                $query->reorder('p_name', 'asc');
            }
            if ($sortBy === 'l_stock') {
                $query->reorder('p_name', 'desc');
            }
            logger()->info($query->toSql());
        })
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()->action(function ($record) {
                    $record->status = 1;
                    $record->save();

                    $record->delete(); // soft delete
                    })
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
    public static function updateProductName($set, $get)
    {
    $productId = $get('product_id');
    $qua = $get('quantity');
    $unitId = $get('unit_id');
    $pkId = $get('packing_type_id');

    if (!$productId || !$unitId || !$pkId || !$qua) {
        return;
    }

    $product = \App\Models\Product::find($productId)?->name;
    $unit = \App\Models\Unit::find($unitId)?->symbol;
    $pk = \App\Models\PackingType::find($pkId)?->name;


    $set('p_name', strtoupper("{$product}_{$qua}{$unit}_{$pk}"));
}
}
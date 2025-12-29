<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Purchase';

    protected static ?string $navigationLabel = 'Product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->disabled()
                ->dehydrated(true)
                ->unique(ignoreRecord:true)  // important: save value to DB
                ->required(),
                Forms\Components\Select::make('brand_id')->label('Brand')
                ->relationship('brand','name')
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::updateProductName($set, $get);
                })->required(),
                Forms\Components\Select::make('category_id')->label('Category')
                ->relationship('category','name')
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::updateProductName($set, $get);
                })->required(), 

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
                Tables\Columns\TextColumn::make('name')->label('Product Name')->searchable(),
                Tables\Columns\TextColumn::make('brand.name')->label('Brand Name')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category Name')->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Active\InActive')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'Active' : 'Inactive'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function updateProductName($set, $get)
    {
    $brandId = $get('brand_id');
    $categoryId = $get('category_id');

    if (!$brandId || !$categoryId) {
        return;
    }

    $brand = \App\Models\Brand::find($brandId)?->name;
    $category = \App\Models\Category::find($categoryId)?->name;

    $set('name', strtoupper("{$brand}_{$category}"));
    }

}

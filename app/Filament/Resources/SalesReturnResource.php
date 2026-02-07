<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesReturnResource\Pages;
use App\Filament\Resources\SalesReturnResource\RelationManagers;
use App\Models\salesOrderDetails;
use App\Models\SalesReturn;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesReturnResource extends Resource
{
    protected static ?string $model = SalesReturn::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

        protected static ?string $navigationGroup = 'Sales';


    public static function form(Form $form): Form
    {
    return $form->schema([
        Hidden::make('so_id'),
        Hidden::make('customer_id'),

        Section::make('Sales Order Info')
            ->schema([
                
                TextInput::make('customer_name')->disabled()->dehydrated(true),
                TextInput::make('return_no')->disabled()->dehydrated(true),
                DatePicker::make('return_date')->disabled()->dehydrated(true),
            ])
            ->columns(2),

        Repeater::make('items')
            ->schema([
                Hidden::make('so_detail_id'),
                Hidden::make('pv_id'),

                TextInput::make('product_name')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('sold_qty')->disabled()->label('Quantity'),
                TextInput::make('already_returned_qty')->disabled()->label('Existing Return'),

                TextInput::make('return_qty')->label('To be Return')
                    ->numeric()
                    ->minValue(0)
                    ->rule(fn (Get $get) =>
                        'lte:' . ($get('sold_qty') - $get('already_returned_qty'))
                    ),

                TextInput::make('rate')->disabled()->dehydrated(true),
                //Hidden::make('amount'),

            ])
           ->addable(false)
           ->columnSpanFull(true)
            ->columns(6),
            Section::make('Total Preview')
            ->schema([
                TextInput::make('total_amount')->disabled()->dehydrated()
            ])
    ]);
    }   

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('return_no')->label('Return No'),
                TextColumn::make('salesOrder.so_number')->label('sales Order No')
                ->url(fn ($record) =>
                    route(
                        'filament.admin.resources.sales-orders.edit',
                        $record->so_id
                    )
                )->color('primary'),
                TextColumn::make('customer.name')->label('Customer Name'),
                TextColumn::make('return_date')->date()->label('Date'),
                TextColumn::make('refund_amount')->label('Refund Amount')->money('INR'),
                TextColumn::make('status')
                 ->color(fn ($state) => $state == 'confirmed' ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSalesReturns::route('/'),
            'create' => Pages\CreateSalesReturn::route('/create'),
            'edit' => Pages\EditSalesReturn::route('/{record}/edit'),
        ];
    }
}

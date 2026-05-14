<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers\PurchaseOrdersRelationManager;
use App\Filament\Resources\VendorResource\RelationManagers\VendorPaymentsRelationManager;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $navigationLabel = 'Vendor';

    public static function form(Form $form): Form
    {
        return $form
             ->schema([
                Forms\Components\TextInput::make('name')->label('Vendor Name')->required()->maxLength(255),
                Forms\Components\TextInput::make('contactno')->label('Phone No')->required()->maxLength(10),
                Forms\Components\TextInput::make('whatsappno')->label('Whatsapp No'),
                Forms\Components\TextInput::make('location')->label('Location'),
                Forms\Components\TextInput::make('mill_name')->label('Mill/Shop Name')->required()->unique(ignoreRecord:true),
                Forms\Components\TextInput::make('gst_number')->label('GST Number')->unique(ignoreRecord:true),
                Forms\Components\Radio::make('status')
                ->options([
                    0 =>"Active",
                    1=>"InActive"
                ])->required()->default(0),
                Forms\Components\Select::make('payment_settlement_type')
                ->options([
                    1 =>"Bill-Wise",
                    2 =>"On-Account"
                ])->required()->default(1)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
             ->columns([
                Tables\columns\TextColumn::make('name')->label('Vendor Name')->sortable(true),
                Tables\Columns\TextColumn::make('mill_name')->label('Mill/Shop Name')->searchable(),
                Tables\Columns\TextColumn::make('contactno')->label('PhoneNo'),
                Tables\Columns\TextColumn::make('status')->label('Status')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'Active' : 'Inactive'),

            ])
            ->defaultSort('name','asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->label('Payment')->icon('heroicon-o-clock')->color('primary'),
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
            PurchaseOrdersRelationManager::class,
            VendorPaymentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
            'view' => Pages\ViewVendor::route('/{record}'),

        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

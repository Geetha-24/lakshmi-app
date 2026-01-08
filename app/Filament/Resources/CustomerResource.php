<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\PaymentRelationManager;
use App\Filament\Resources\CustomerResource\RelationManagers\SalesOrderRelationManager;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $navigationLabel = 'Customer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Customer Name')->required()->maxLength(255),
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
                Forms\Components\Radio::make('type')
                ->options([
                    0 =>"Whole Sale",
                    1=>"Retail Sale"
                ])->required()->default(0)
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\columns\TextColumn::make('name')->label('Customer Name'),
                Tables\Columns\TextColumn::make('mill_name')->label('Shop Name')->searchable(),
                Tables\Columns\TextColumn::make('contactno')->label('Phone No'),
                Tables\Columns\TextColumn::make('balance')
                ->label('Balance')
                ->getStateUsing(function ($record) {
                    return \App\Models\CustomersLedger::where('c_id', $record->id)
                        ->orderByDesc('created_at')
                        ->value('balance_after') ?? 0;
                })
                ->money('INR', true),
                Tables\Columns\TextColumn::make('type')->label('Customer Type')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'WholeSale' : 'Retail'),
                Tables\Columns\TextColumn::make('status')->label('Status')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'Active' : 'Inactive'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                 Tables\Actions\ViewAction::make()->label('History')->icon('heroicon-o-clock')
                ->color('primary'),
                Tables\Actions\DeleteAction::make()->action(function ($record) {
                    $record->status = 1;
                    $record->save();

                    $record->delete(); // soft delete
                    }),
               

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
            SalesOrderRelationManager::class,
            PaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view'=> Pages\ViewCustomer::route('/{record}')
        ];
    }
}

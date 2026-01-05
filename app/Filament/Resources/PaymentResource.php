<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\CustomersLedger;
use App\Models\Payment;
use App\Models\SalesOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payment')
                ->schema([

                    Select::make('c_id')
                        ->relationship('customer', 'name')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                             if (!$state) {
                                    return;
                                }
                            $set('sales_order_id', null);
                            $set('order_total', null);
                            $set('order_paid', null);
                            $set('order_balance', null);

                        //Total balance of the customer
                        $balance = CustomersLedger::where('c_id', $state)
                            ->orderByDesc('id')
                            ->value('balance_after') ?? 0;


                        $set('order_balance', $balance);
                        $set('amount',$balance);
                        $set('payment_date', now()->toDateString());
                        $set('payment_mode_id', '1');
                    }),


                    Select::make('so_id')
                        ->relationship('salesOrder', 'so_number')
                        ->options(fn ($get) =>
                        SalesOrder::where('customer_id', $get('customer_id'))
                            ->where('balance_amount', '>', 0)
                            ->pluck('so_number', 'id')
                    )
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {

                        if (!$state) {
                            $set('order_total', null);
                            $set('order_paid', null);
                            $set('order_balance', null);
                            return;
                        }

                        $order = SalesOrder::find($state);
                        //sales order account info
                        $set('order_total', $order->total_amount);
                        $set('order_paid', $order->paid_amount);
                        $set('order_balance', $order->balance_amount);

                        // Default payment amount = balance
                        $set('amount', $order->balance_amount);
                    }),

                    DatePicker::make('payment_date')
                        ->default(now())
                        ->required(),

                    Placeholder::make('order_total_preview')
                    ->label('Order Total')
                    ->content(fn ($get) =>
                        $get('order_total')
                            ? number_format($get('order_total'), 2)
                            : '—'
                    ),

                Placeholder::make('paid_amount_preview')
                    ->label('Paid Amount')
                    ->content(fn ($get) =>
                        $get('order_paid')
                            ? number_format($get('order_paid'), 2)
                            : '—'
                    ),

                Placeholder::make('order_balance_preview')
                    ->label('Order Balance')
                    ->content(fn ($get) =>
                        $get('order_balance')
                            ? number_format($get('order_balance'), 2)
                            : '—'
                    ),

                    TextInput::make('amount')
                        ->label('Paying Now')
                        ->numeric()
                        ->required(),

                    Select::make('payment_mode_id')
                    ->relationship('paymentModeMaster','mode')
                    ->required(),
                        

                    TextInput::make('reference_no')
                        ->label('Reference No'),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}

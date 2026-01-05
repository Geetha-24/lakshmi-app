<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewSalesOrder extends ViewRecord
{
    protected static string $resource = SalesOrderResource::class;

    /* ===============================
     * HEADER ACTIONS
     * =============================== */
    public function getHeaderActions(): array
    {
        return [

            /* ✅ CONFIRM ORDER */
            Actions\Action::make('confirm')
                ->label(fn ($record) =>
                    $record?->status === 'confirmed'
                        ? 'CONFIRMED'
                        : 'CONFIRM ORDER'
                )
                ->icon('heroicon-o-check-circle')
                ->color(fn ($record) =>
                    $record?->status === 'confirmed'
                        ? 'success'
                        : 'primary'
                )
                ->disabled(fn ($record) =>
                    $record?->status === 'confirmed'
                )
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'draft')
                ->action(fn () =>
                    \App\Services\SalesOrderService::confirm($this->record)
                ),

            /* 💰 MAKE PAYMENT */
            Actions\Action::make('payment')
                ->label('Make Payment')
                ->icon('heroicon-o-currency-rupee')
                ->color('primary')
                ->visible(fn () =>
                    $this->record->status === 'confirmed'
                    && $this->record->balance_amount > 0
                )
                ->url(fn () =>
                    route('filament.admin.resources.payments.create', [
                        'so_id' => $this->record->id,
                    ])
                ),
        ];
    }

    /* ===============================
     * VIEW DATA
     * =============================== */
    public function infolist(Infolist $infolist): Infolist
    {
        return Infolist::make()
                ->record($this->record) // ✅ THIS IS THE FIX

            ->schema([

                /* ===============================
                 * ORDER INFO
                 * =============================== */
                Section::make('Sales Order')
                    ->columns(4)
                    ->schema([

                        TextEntry::make('so_number')
                            ->label('SO No'),

                        TextEntry::make('customer.name')
                            ->label('Customer'),

                        TextEntry::make('order_date')
                            ->date(),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'draft'     => 'gray',
                                'confirmed' => 'success',
                                'CLOSED'    => 'primary',
                                default     => 'secondary',
                            }),
                    ]),

                /* ===============================
                 * ITEMS
                 * =============================== */
                Section::make('Items')
                    ->schema([

                        RepeatableEntry::make('salesOrderDetails')
                            ->columns(5)
                            ->schema([

                                TextEntry::make('productVariant.p_name')
                                    ->label('Product'),

                                TextEntry::make('quantity'),

                                TextEntry::make('sold_price')
                                    ->label('Rate')
                                    ->money('INR'),

                                TextEntry::make('line_total')
                                    ->label('Amount')
                                    ->money('INR'),
                            ]),
                    ]),

                /* ===============================
                 * TOTALS
                 * =============================== */
                Section::make('Totals')
                    ->columns(4)
                    ->schema([

                        TextEntry::make('subtotal')
                            ->money('INR'),

                        TextEntry::make('tax_amount')
                            ->label('Tax')
                            ->money('INR'),

                        TextEntry::make('total_amount')
                            ->label('Total')
                            ->money('INR'),

                        TextEntry::make('payment_status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'UNPAID'   => 'danger',
                                'PARTIAL'  => 'warning',
                                'PAID'     => 'success',
                                default    => 'secondary',
                            }),
                    ]),

                /* ===============================
                 * PAYMENT SUMMARY
                 * =============================== */
                Section::make('Payment Summary')
                    ->columns(3)
                    ->schema([

                        TextEntry::make('paid_amount')
                            ->label('Paid')
                            ->money('INR'),

                        TextEntry::make('balance_amount')
                            ->label('Balance')
                            ->money('INR')
                            ->color(fn ($state) =>
                                $state > 0 ? 'danger' : 'success'
                            ),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since(),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use App\Filament\Resources\VendorResource\Widgets\VendorFinancialStats;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Forms\Components\Select;
use App\Services\VendorPaymentService;
use Filament\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderWidgets(): array
    {
        return[
            VendorFinancialStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payVendor')
    ->label('Pay Vendor')
    ->form([
        DatePicker::make('payment_date')->required(),
        Select::make('payment_mode')
            ->options(\App\Models\PaymentMode::query()
            ->pluck('mode', 'id')
            ->toArray())->required()->searchable(),
        TextInput::make('amount')->numeric()->required(),
        TextInput::make('cheque_no')
            ->visible(fn ($get) => $get('payment_mode') == 4),
    ])
    ->action(function (array $data) {
        VendorPaymentService::makePayment($this->record, $data);
    })
    ->successNotificationTitle('Payment recorded successfully')

        ];
    }

     protected function getRedirectUrl(): string
    {
        return $this->previousUrl ? $this->getResource()::getUrl('index') : $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title("Vendor Updation")
        ->body("The Vendor has been updated successfully");
    
    }

    public function infolist(Infolist $infolist): Infolist
    {
        
        return Infolist::make()
            ->record($this->record)
            ->schema([
                Section::make('Vendor Info')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('name')
                                ->label('Vendor Name'),

                            TextEntry::make('contactno')
                                ->label('Contact No'),

                            TextEntry::make('whatsappno')
                                ->label('Whatsapp No'),
                                
                            TextEntry::make('payment_settlement_type')
                                ->label('Settlement Mode')
                                ->formatStateUsing(fn ($state) => (string) $state === '1' ? 'Bill-Wise' : 'On-Account'),
                        ]),
                    ])
                    ->collapsible(false),
            ]);
    }

}

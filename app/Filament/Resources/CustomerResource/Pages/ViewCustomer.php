<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerResource\Widgets\CustomerFinancialStats;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\DB;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerFinancialStats::class,
        ];
    }

    

    public function infolist(Infolist $infolist): Infolist
    {
        
        return Infolist::make()
            ->record($this->record)
            ->schema([
                Section::make('Customer Info')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('name')
                                ->label('Customer Name'),

                            TextEntry::make('contactno')
                                ->label('Contact No'),

                            TextEntry::make('whatsappno')
                                ->label('Whatsapp No'),
                                
                            TextEntry::make('type')
                                ->label('Mode')
                                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'WholeSale' : 'Retail'),
                        ]),
                    ])
                    ->collapsible(false),
            ]);
    }

}

<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Livewire\Attributes\Url;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Tables\CustomerPaymentTable;
use App\Filament\Tables\VendorPaymentTable;


class PaymentList extends Page implements HasTable
{
    use InteractsWithTable;
    use CustomerPaymentTable;
    use VendorPaymentTable;

    protected static string $resource = PaymentResource::class;

     protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $title = 'Payments';

    protected static string $view = 'filament.resources.payment-resource.pages.payment-list';

    public string $activeTab = 'customer';


    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
                $this->resetTable(); // 🔥 THIS IS THE FIX

    }

     public function table(Table $table): Table
    {
        return $this->activeTab === 'customer'
            ? $this->customerPaymentTable($table)
            : $this->vendorPaymentTable($table);
    }
}

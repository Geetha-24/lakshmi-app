<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
// use Filament\Infolists\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use App\Services\PostStockItem;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;





class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Purchase';
    protected static ?string $navigationLabel = 'purchaseOrder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vendor_id')->label('Select Vendor')
                ->relationship('vendor','name')->required(),
                Forms\Components\TextInput::make('invoice_number')->required(),
                Forms\Components\DatePicker::make('invoice_date')->required(),
                Forms\Components\TextInput::make('gross_amount')->required()->default(0),
                Forms\Components\TextInput::make('tax_amount')->required()->default(0),
                Forms\Components\TextInput::make('discount_amount')->required()->default(0),
                Forms\Components\TextInput::make('net_amount')->required()->default(0),
                Forms\Components\TextInput::make('paid_amount')->required()->default(0),
                Forms\Components\TextInput::make('due_amount')->required()->default(0),
                Forms\Components\Select::make('payment_status')
                ->options([
                    0 =>"On Cash",
                    1=>"Cheque",
                    2=>'Credit'
                ])->required()->default(2),
                Forms\Components\Radio::make('status')
                ->options([
                    0 =>"Active",
                    1=>"InActive"
                ])->required()->default(0)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.name')->label('Vendor')->searchable(),
                Tables\Columns\TextColumn::make('invoice_date')->label('Date'),
                Tables\Columns\TextColumn::make('net_amount')->label('Bill Amount'),
                Tables\Columns\TextColumn::make('payment_status')->label('Payment Type')
                ->formatStateUsing(fn ($state) => 
                    $state === '0' 
                        ? 'On Cash' 
                        : ($state === '1' 
                            ? 'Cheque' 
                            : 'Credit'
                        )
                    )            
            ])
        //     ->recordExpansion(function ($record) {
        //           return 
        //           \Filament\Infolists\Infolist::make()
        //         ->schema([
        //              Section::make('Purchase Order Details')
        //         ->schema([
        //            RepeatableEntry::make('PurchaseOrderDetail')
        //                 ->relationship('PurchaseOrderDetail')
        //                 ->schema([
        //                     TextEntry::make('PurchaseOrderDetail.ProductVariant.p_name')->label('Product'),
        //                    TextEntry::make('quantity')->label('Qty'),
        //                     TextEntry::make('unit_price')->label('Unit Price'),
        //                     TextEntry::make('total_amount')->label('Amount'),
        //                 ])
        //                 ->columns(4)
        //                 ->columnSpanFull(),
        //         ])
        //         ->collapsible(),
        // ]);
        //     })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('addDetails')
                ->label('+ OrderDetails')
                ->url(fn ($record) => PurchaseOrderDetailResource::getUrl('create', [
                    'purchase_order_id' => $record->id,
                ])),
                Tables\Actions\ViewAction::make(),

            //    Tables\Actions\Action::make('expand')
            //     ->label('')
            //     ->iconButton()
            //     ->icon('heroicon-m-chevron-down')
            //     ->toggleRecordExpansion(),
               
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                ]);
           
    }

    // public Static function infolist(Infolist $infolist): Infolist
    // {
        
    //     return $infolist->schema([
    //         Section::make('Bill Information')
    //             ->schema([
    //                 Infolists\Components\TextEntry::make('invoice_number'),
    //                 Infolists\Components\TextEntry::make('invoice_date'),
    //                 Infolists\Components\TextEntry::make('net_amount'),
    //                 Infolists\Components\TextEntry::make('paid_amount'),
    //                 Infolists\Components\TextEntry::make('due_amount'),

    //             ]),
    //              Section::make('Purchase Order Details Info')
    //                 // Header Row         
    //                 ->schema([
    //                     Infolists\Components\Grid::make(6)
    //                                 ->schema([
    //                                     Infolists\Components\TextEntry::make('header_product')
    //                                         ->label('')
    //                                         ->default('Product')
    //                                         ->columnSpan(1)->weight('bold'),

    //                                     Infolists\Components\TextEntry::make('header_qty')
    //                                         ->label('')
    //                                         ->default('Qty')
    //                                         ->columnSpan(1)->weight('bold'),

    //                                     Infolists\Components\TextEntry::make('header_rate')
    //                                         ->label('')
    //                                         ->default('Rate')
    //                                         ->columnSpan(1)->weight('bold'),

    //                                     Infolists\Components\TextEntry::make('header_amount')
    //                                         ->label('')
    //                                         ->default('Amount')
    //                                         ->columnSpan(1)->weight('bold'),
    //                                      Infolists\Components\TextEntry::make('header_posted_qty')
    //                                         ->label('')
    //                                         ->default('Posted Qty')
    //                                         ->columnSpan(1)->weight('bold'),
                                        
    //                                         Infolists\Components\TextEntry::make('header_actions')
    //                                         ->label('')
    //                                         ->default('Actions')
    //                                         ->columnSpan(1)->weight('bold'),
    //                                 ]),
                            
    //                             Infolists\Components\RepeatableEntry::make('purchaseOrderDetails')->label('') 
    //                             ->state(fn ($record) => $record->purchaseOrderDetails)
    //                             ->schema([ 
    //                             // Data Row
    //                             Infolists\Components\Grid::make(6)
    //                                 ->schema([
    //                                     Infolists\Components\TextEntry::make('ProductVariant.p_name')->label('')
    //                                     ->state(fn ($record) => $record->productVariant->p_name)
    //                                     ->url(fn ($record) =>
    //                                         \App\Filament\Resources\PurchaseOrderDetailResource::getUrl(
    //                                             'edit',
    //                                             ['record' => $record->id]
    //                                         )
    //                                     )
    //                                     ->openUrlInNewTab(false) // optional
    //                                     ->color('primary'),
    //                                     //->state(fn($record)=>$record->purchaseOrderDetails),
    //                                     Infolists\Components\TextEntry::make('qunatity')
    //                                     ->state(fn ($record) => $record->qunatity)->label(''),
    //                                     Infolists\Components\TextEntry::make('unit_price')
    //                                     ->state(fn ($record) => $record->unit_price)->label(''),
    //                                     Infolists\Components\TextEntry::make('total_amount')
    //                                     ->state(fn ($record) => $record->total_amount)->label(''),
    //                                      Infolists\Components\TextEntry::make('posted_qty')
    //                                     ->state(fn ($record) => $record->posted_qty)->label(''),
    //                                     Infolists\Components\Actions::
    //                                     make([
    //                                         Action::make('postStock')
    //                                             ->label('Post Stock')
    //                                             ->icon('heroicon-m-arrow-up-tray')
    //                                             ->color('primary')
    //                                             ->visible(fn($record) =>
    //                                                $record->posted_qty < $record->qunatity
    //                                             )
    //                                         ->form([
    //                                                 TextInput::make('qty')
    //                                                     ->label('Quantity to Post')
    //                                                     ->numeric()
    //                                                     ->required()
    //                                                     ->minValue(1)
    //                                                     ->maxValue(fn ($record) =>
    //                                                         $record->qunatity - $record->posted_qty
    //                                                     ),
    //                                             ])
    //                                             ->action(function ($record, array $data) {
    //                                                 app(PostStockItem::class)
    //                                                     ->handle($record, $data['qty']);
    //                                             }),
    //                                     ])
                                        
    //                                 ])
    //                         ])->contained(false)
    //                 ])->columnSpanFull(),
    //         ]);
    // }

    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
            'view' => Pages\ViewPurchaseOrder::route('/{record}'),

        ];
    }
    
    // protected function getTableRecordDetails(): ?Closure
    // {   
    //     return fn ($record) => view(
    //         'filament.tables.purchase-order-details',
    //         ['order' => $record]
    //     );
    // }
}

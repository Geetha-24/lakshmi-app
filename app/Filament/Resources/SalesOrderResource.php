<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesOrderResource\Pages;
use App\Filament\Resources\SalesOrderResource\RelationManagers;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use App\Services\SalesOrderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Awcodes\TableRepeater\Components\TableRepeater;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Filters\SelectFilter;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $navigationLabel = 'Sales Order';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //              Section::make('Sales Order')
    //         ->schema([

    //             TextInput::make('so_number')
    //                 ->default(fn () => SalesOrder::generateSoNumber())
    //                 ->disabled()
    //                 ->dehydrated(true),

    //             Select::make('customer_id')
    //                 ->relationship('Customer', 'name')
    //                 ->required(),

    //             DatePicker::make('order_date')
    //                 ->default(now())
    //                 ->required(),

    //             Repeater::make('items')
    //                 ->disabled(fn ($record) => optional($record)->status === 'confirmed')
    //                 ->schema([
    //                     Select::make('pv_id')
    //                         ->label('Product')
    //                         ->options(ProductVariant::pluck('p_name', 'id'))
    //                         ->required()
    //                          ->searchable()
    //                         ->preload()
    //                         ->live() // IMPORTANT
    //                         ->afterStateUpdated(function ($state, callable $set) {
    //                             $data = SaleService::getStockAndSellingPrice($state);
    //                             $set('available_stock',$data['avl_stock']);
    //                             $set('sold_price',$data['selling_price']);
    //                             $set('quantity',1);
    //                         }),
    //                         TextInput::make('available_stock')
    //                         ->label('Stock')
    //                         ->disabled()
    //                         ->dehydrated(false),

    //                         TextInput::make('quantity')
    //                             ->numeric()
    //                             ->required()
    //                             ->live()
    //                             ->rule(function (callable $get) {
    //                                 return function ($attribute, $value, $fail) use ($get) {
    //                                     if ($value > $get('available_stock')) {
    //                                         $fail('Quantity exceeds available stock');
    //                                     }
    //                                 };
    //                             })
    //                             ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
    //                                     self::calculateLineAmount($set, $get)
    //                             ),
    //                     TextInput::make('fixed_selling_price')->numeric()->visible(false)->dehydrated(true),

    //                     TextInput::make('sold_price')
    //                         ->numeric()
    //                         ->required()
    //                         ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
    //                                 self::calculateLineAmount($set, $get)
    //                          ),

    //                     TextInput::make('line_total')
    //                         ->numeric()
    //                         ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
    //                             self::calculateLineAmount($set, $get)
    //                         ),
    //                 ])
    //                 ->addActionLabel('Add Item')
    //                 ->columns(4)
    //                 ->live()
    //                 ->afterStateUpdated(function ($state, callable $set) {
    //                      $subtotal = collect($state)
    //                             ->sum(fn ($item) => (float) ($item['line_total'] ?? 0));

    //                         $set('subtotal', $subtotal);
    //                         $set('total_amount', $subtotal);
    //                 })
    //                 ->afterStateHydrated(function ($component, $state, $record) {
    //                     if (! $record) return;

    //                     // Map sale items into repeater state
    //                     $component->state(
    //                         $record->salesOrderDetails->map(fn($item) => [
    //                             'pv_id' => $item->pv_id,
    //                             'quantity' => $item->quantity,
    //                             'sold_price' => $item->sold_price,
    //                             'line_total' => $item->line_total,
    //                             'available_stock' => $item->quantity ?? 0,
    //                         ])->toArray()
    //                     );
    //                 }),

    //             TextInput::make('subtotal')->numeric()->disabled(),
    //             TextInput::make('total_amount')->numeric()->disabled(),

    //             ]),
                
    //         ]);

    // }


    public static function form(Form $form): Form
    {
        return $form->schema([

            /* ===============================
            * ORDER INFO
            * =============================== */
            Section::make('Sales Order')
                ->columns(4)
                ->schema([

                    TextInput::make('so_number')
                        ->default(fn () => SalesOrder::generateSoNumber())
                        ->disabled()
                        ->dehydrated(),

                    Select::make('customer_id')
                        ->relationship('Customer', 'name')
                        ->preload()
                        ->searchable()
                        ->required(),

                    DatePicker::make('order_date')
                        ->default(now())
                        ->required(),

                    Placeholder::make('status')
                        ->content(fn ($record) => $record?->status ?? 'DRAFT'),
                ]),

            /* ===============================
            * ITEMS
            * =============================== */
            Section::make('Items')
                ->schema([

                    Repeater::make('salesOrderDetails')
                        ->relationship('salesOrderDetails')
                        ->disabled(fn ($record) => $record?->status === 'confirmed')
                        ->columns(6)
                        ->schema([
                            Select::make('pv_id')
                                ->label('Product')
                                ->options(ProductVariant::pluck('p_name', 'id'))
                                ->preload()
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $data = SalesOrderService::getStockAndSellingPrice($state);
                                    $set('sold_price',$data['selling_price']);
                                    $set('quantity',1);
                                }),

                            TextInput::make('quantity')
                                ->numeric()
                                ->live(onBlur:true)
                                ->required(),

                            TextInput::make('sold_price')
                                ->label('Rate')
                                ->numeric()
                                ->live(onBlur:true)
                                ->required(),

                            Placeholder::make('line_total')
                                ->label('Amount')
                                ->content(fn ($get) =>
                                    number_format(
                                        (float) ($get('quantity') ?? 0) * (float) ($get('sold_price') ?? 0),
                                        2
                                    )
                                ),
                            
                        ])
                        ->saveRelationshipsUsing(function () {
                            // 🚫 STOP FILAMENT FROM SAVING ANYTHING
                            return;
                        })
                        
                        ->addActionLabel('Add Item')
                        ->reorderable(false),
                        
                ]),

            /* ===============================
            * TOTALS PREVIEW
            * =============================== */
            Section::make('Totals')
                ->columns(3)
                ->schema([

                    Placeholder::make('subtotal_preview')
                        ->label('Subtotal')
                        ->content(function ($get) {
                            $taxRate = 5; // 🔴 change if needed

                            $total = collect($get('salesOrderDetails') ?? [])
                                ->sum(fn ($item) =>
                                    (float) ($item['quantity'] ?? 0) *
                                    (float) ($item['sold_price'] ?? 0)
                                );

                            if ($total <= 0) {
                                return '0.00';
                            }

                            $base = round(
                                $total / (1 + ($taxRate / 100)),
                                2
                            );

                            return number_format($base, 2);
                        }),

                    Placeholder::make('tax_preview')
                        ->label('Tax')
                        ->content(function ($get) {

                            $taxRate = 5;

                            $total = collect($get('salesOrderDetails') ?? [])
                                ->sum(fn ($item) =>
                                    (float) ($item['quantity'] ?? 0) *
                                    (float) ($item['sold_price'] ?? 0)
                                );

                            if ($total <= 0) {
                                return '0.00';
                            }

                            $base = round(
                                $total / (1 + ($taxRate / 100)),
                                2
                            );

                            $tax = round($total - $base, 2);

                            return number_format($tax, 2);
                        }),

                    Placeholder::make('total_preview')
                        ->label('Total')
                        ->content(function ($get) {

                        $total = collect($get('salesOrderDetails') ?? [])
                            ->sum(fn ($item) =>
                                (float) ($item['quantity'] ?? 0) *
                                (float) ($item['sold_price'] ?? 0)
                            );

                        return number_format($total, 2);
                    }),
                ]),
            ]); 
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('so_number')->label('Sales.No')->searchable(),
                Tables\Columns\TextColumn::make('Customer.name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('saleItems_count')
                    ->label('Total Items')
                    ->getStateUsing(fn ($record) => $record->salesOrderDetails->sum('quantity')),
                Tables\Columns\TextColumn::make('order_date')->label('sales Date')->searchable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Total Amount'),
                // Tables\Columns\TextColumn::make('profit')->badge()
                // ->label('Profit / Loss')
                // ->getStateUsing(fn ($record) => $record->status == "draft" ? 0.0 : $record->salesOrderDetails->sum(fn($item) => ($item->sold_price - $item->SalesBatch->purchase_price) * $item->quantity))
                // ->formatStateUsing(fn ($state) => number_format($state, 2))
                // ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                // ->sortable(),
                Tables\Columns\TextColumn::make('total_profit')->badge()
                 ->label('Profit / Loss')
                 ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
                 Tables\Columns\TextColumn::make('return_status')
                ->label('Return')
                ->getStateUsing(function ($record) {
                    $returned = $record->salesOrderdetails->sum('returned_qty');
                    $total = $record->salesOrderdetails->sum('quantity');

                    return match (true) {
                        $returned == 0 => 'No Return',
                        $returned < $total => 'Partial',
                        default => 'Full',
                    };
                })
                ->colors([
                    'gray' => 'No Return',
                    'warning' => 'Partial',
                    'success' => 'Full',
                ])




            ])
           ->filters([
            SelectFilter::make('status')
                ->options([
                    'draft' => 'Draft',
                    'confirmed' => 'Confirmed',
                ]),
            SelectFilter::make('sort_by')
                ->label('Sort By')
                ->options([
                    'latest' => 'Latest',
                    'oldest' => 'Oldest'

                ])
                ->query(fn (Builder $query) => $query), // 🔥 stop WHERE clause
        ])
        ->modifyQueryUsing(function (Builder $query, $livewire) {

            $sortBy = data_get($livewire, 'tableFilters.sort_by.value');
            logger()->info($query->toSql());
            if ($sortBy === 'latest') {
                $query->reorder('created_at', 'desc');
            }

            if ($sortBy === 'oldest') {
                $query->reorder('created_at', 'asc');
            }
            logger()->info($query->toSql());
        })
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListSalesOrders::route('/'),
            'create' => Pages\CreateSalesOrder::route('/create'),
            'edit' => Pages\EditSalesOrder::route('/{record}/edit'),
            'view' => Pages\ViewSalesOrder::route('/{record}'),

        ];
    } 
    
    public static function calculateLineAmount($set,$get)
    {
         $rate = (float) ($get('sold_price') ?? 0);
         $qty  = (float) ($get('quantity') ?? 0);

         $lineTotal = $qty * $rate;
         $set('line_total',round($lineTotal,2));
    }

}

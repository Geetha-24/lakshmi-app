<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderDetailResource\Pages;
use App\Filament\Resources\PurchaseOrderDetailResource\RelationManagers;
use App\Models\MarginConfig;
use App\Models\PurchaseOrderDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseOrderDetailResource extends Resource
{
    protected static ?string $model = PurchaseOrderDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Purchase';

    protected static ?string $navigationLabel = 'PODetail';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('po_id')->label('PurchaseOrder')
                    ->default(request()->query('purchase_order_id'))->readOnly(),

                Forms\Components\Select::make('pv_id')->label('Select Product Variant')
                    ->relationship('ProductVariant','p_name')
                    ->required()
                    ->disableOptionWhen(function ($value, callable $get) {
                        return PurchaseOrderDetail::where('po_id', $get('po_id'))
                            ->where('pv_id', $value)
                            ->exists();
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                    $margin = MarginConfig::where('pv_id', $state)
                        ->value('profit_amount');

                    $set('profit_value', $margin ?? 0);
                }),

                Forms\Components\TextInput::make('qunatity')->label('Quantity')
                ->required()->live(onBlur:true)
                   ->afterStateUpdated(function ($state,callable $set, callable $get) {
                    //$set('total_amount', $state * (float) $get('unit_price'));
                            self::calculateAmounts($set, $get);
                            self::updateLandCost($set, $get);


                     }),
                Forms\Components\TextInput::make('unit_price')->label('Unit Price')->required()->live(onBlur:true)
                     ->afterStateUpdated(function ($state,callable $set, callable $get) {
                        //$set('total_amount', (float) $get('qunatity') * $state);
                                self::calculateAmounts($set, $get);
                                self::updateLandCost($set, $get);


                    }),

                Forms\Components\TextInput::make('total_amount')->label('Total Amount')
                    ->required()->readOnly()->numeric(),

                 Forms\Components\Radio::make('status')->label('Status')
                ->options([
                    0 =>"Active",
                    1=>"InActive"
                ])->required()->default(0),

                Forms\Components\Section::make('Tax Information')->schema([

                Forms\Components\Toggle::make('apply_tax')->label('Apply Tax ')
                    ->required()->default(false)->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if ($state && ($get('tax_percentage') == 0 || $get('tax_percentage') === null)) {
                        $set('tax_percentage', 5); // default tax
                    }

                    if (! $state) {
                        $set('tax_percentage', 0);
                        $set('tax_amount', 0);
                        $set('inc_tax_total_amount', $get('total_amount'));
                    }

                    self::calculateAmounts($set, $get);
                    self::updateLandCost($set, $get);

                }),
                       

                //Tax Calculation
                Forms\Components\TextInput::make('tax_percentage')->label('Tax Percentage')->numeric()
                    ->default(5)
                    ->required(fn ($get) => $get('apply_tax'))
                    ->visible(fn ($get) => $get('apply_tax'))
                    ->afterStateUpdated(fn ($state, $set, $get) =>
                        self::calculateAmounts($set, $get)
                    ),
                                    
                Forms\Components\TextInput::make('tax_amount')->label('Tax Amount')->readOnly()
                ->required(fn (callable $get) => $get('apply_tax') === true) // ✅ conditional
                ->visible(fn (callable $get) => $get('apply_tax') === true)
                ->live(),
                    
                 Forms\Components\TextInput::make('inc_tax_total_amount')->label('Total Amount(Inc Tax)')
                     ->required(fn (callable $get) => $get('apply_tax') === true) // ✅ conditional
                    ->readOnly()->numeric()->visible(fn (callable $get) => $get('apply_tax') === true),
                ]),
                
                
                //Expense Cost

                Forms\Components\Section::make('Costing Rule')
                ->schema([
                Forms\Components\TextInput::make('delivery_charge_per_unit')
                ->numeric()
                ->default(0)
                ->live()
                ->afterStateUpdated(fn ($state, $set, $get) =>
                        self::updateLandCost($set, $get)
                    ),

                Forms\Components\TextInput::make('lorry_charge_per_unit')
                    ->numeric()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(fn ($state, $set, $get) =>
                        self::updateLandCost($set, $get)
                    ),

                Forms\Components\TextInput::make('profit_value')
                ->label('Profit Value')
                ->numeric()
                ->default(0)
                ->required()
                ->live()
                ->afterStateUpdated(fn ($state, $set, $get) =>
                        self::updateLandCost($set, $get)
                    ),

                // Forms\Components\Placeholder::make('preview_landed_cost')
                // ->label('Landed Cost / Unit (Preview)')
                // ->content(function (callable $get) {
                //     $unitPrice = (float) ($get('unit_price') ?? 0);
                //     $delivery  = (float) ($get('delivery_charge_per_unit') ?? 0);
                //     $lorry     = (float) ($get('lorry_charge_per_unit') ?? 0);
                //     $profitValue     = (float) ($get('profit_value') ?? 0);
                //     $qty       = (float) ($get('qunatity') ?: 1);

                //     $baseCost = $unitPrice + $delivery + $lorry+$profitValue;

                //     $gstPerUnit = 0;
                //     if ($get('apply_tax')) {
                //         $taxTotal = (float) ($get('tax_amount') ?? 0);
                //         $gstPerUnit = round($taxTotal / $qty, 2);
                //     }

                //     return number_format($baseCost + $gstPerUnit, 2);
                // }),
                Forms\Components\TextInput::make('SP_with_profit')
                ->label('Fixed Selling Price')
                ->readOnly()
                ->required()
                ->afterStateUpdated(fn ($state, $set, $get) =>
                        self::updateLandCost($set, $get)
                    ),
                Forms\Components\TextInput::make('SP_without_profit')
                ->label('SP(Without Profit)')
                ->readOnly()
                ->live()
                ->dehydrated()
                ]),

                 Forms\Components\Section::make('Stock Updation Info')->schema([
 
                //Stock Updation details
                Forms\Components\TextInput::make('posted_qty')->label('Posted Qty')
                    ->required()->default(0)->disabled(),
                Forms\Components\Toggle::make('is_posted')->label('Is Stock Updated')
                    ->required()->default(false)->disabled(),
                Forms\Components\DatePicker::make('posted_at')->disabled(),
                 ]),


            

                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ProductVariant.p_name'),
                Tables\Columns\TextColumn::make('qunatity')->label('Quantity'),
                Tables\Columns\TextColumn::make('unit_price')
                ->label('Unit Price')
                ->money('INR')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SP_without_profit')->label('Purchase Price')->money('INR'),
                Tables\Columns\TextColumn::make('SP_with_profit')->label('Fixed Selling Price')->money('INR'),
                Tables\Columns\TextColumn::make('total_amount')->label('Total Amount'),
                Tables\Columns\TextColumn::make('status')->label('Active\InActive')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'Active' : 'Inactive'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\EditAction::make()
                
                ->tooltip(fn ($record) =>
                    $record->posted_qty > 0
                        ? 'Cannot edit after stock is posted'
                        : null
                )
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
            'index' => Pages\ListPurchaseOrderDetails::route('/'),
            'create' => Pages\CreatePurchaseOrderDetail::route('/create'),
            'edit' => Pages\EditPurchaseOrderDetail::route('/{record}/edit'),
        ];
    }

    public static function calculateAmounts($set, $get)
    {
        $qty = (float) ($get('qunatity') ?? 0);
        $price = (float) ($get('unit_price') ?? 0);
        $taxPercent = (float) ($get('tax_percentage') ?? 0);
        $apply_tax = (bool)($get('apply_tax') ?? false);

        $subtotal = round($qty * $price, 2);

        if($apply_tax && $taxPercent>0)
        {
        $tax = round($subtotal * ($taxPercent / 100), 2);
        $total = round($subtotal + $tax, 2);
        
        $set('total_amount', $subtotal);
        $set('tax_amount', $tax);
        $set('inc_tax_total_amount', $total);
        }else{
         
        $set('total_amount', $subtotal);
        $set('tax_amount', 0);
        $set('inc_tax_total_amount', $subtotal);
        }
        
    }

    public static function updateLandCost($set,$get)
    {
         $unitPrice = (float) ($get('unit_price') ?? 0);
         $delivery  = (float) ($get('delivery_charge_per_unit') ?? 0);
         $lorry     = (float) ($get('lorry_charge_per_unit') ?? 0);
         $profitValue     = (float) ($get('profit_value') ?? 0);
        $qty       = (float) ($get('qunatity') ?: 1);
        $baseCost = $unitPrice + $delivery + $lorry + $profitValue;
        $baseCostWithoutProfit = $baseCost-$profitValue;

        $gstPerUnit = 0;
        if ($get('apply_tax')) {
            $taxTotal = (float) ($get('tax_amount') ?? 0);
            $gstPerUnit = round($taxTotal / $qty, 2);
        }
        $set('SP_without_profit',round($baseCostWithoutProfit + $gstPerUnit, 2));
        $set('SP_with_profit',round($baseCost + $gstPerUnit, 2));
    }


  
}

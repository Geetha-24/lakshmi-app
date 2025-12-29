<?php

namespace App\Filament\Resources\PurchaseOrderResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use App\Services\PostStockItem;
use Illuminate\Validation\ValidationException;


class PurchaseOrderDetailsTable extends BaseWidget
{
    public $record; // injected automatically on View page
    protected int | string | array $columnSpan = 'full';


    protected static ?string $heading = 'Purchase Order Details';

    protected function getTableQuery(): Builder
    {
        return $this->record
            ->purchaseOrderDetails()
            ->getQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('productVariant.p_name')
                ->label('Product')
                ->url(fn ($record) =>
                    route(
                        'filament.admin.resources.purchase-order-details.edit',
                        $record
                    )
                )
                ->color('primary'),

            Tables\Columns\TextColumn::make('qunatity')
                ->label('Qty'),

            Tables\Columns\TextColumn::make('unit_price')
                ->label('Rate'),

            Tables\Columns\TextColumn::make('total_amount')
                ->label('Amount'),

            Tables\Columns\TextColumn::make('posted_qty')
                ->label('Posted Qty'),
                  Tables\Columns\TextColumn::make('Actions')
                ->label('Actions')
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('postStock')
                ->label(fn ($record) =>
                    $record->posted_qty >= $record->qunatity
                        ? 'Stock Updated'
                        : 'Post Stock'
                )
                ->icon(fn ($record) =>
                    $record->posted_qty >= $record->qunatity
                        ? 'heroicon-m-check-circle'
                        : 'heroicon-m-arrow-up-tray'
                )
                ->color(fn ($record) =>
                    $record->posted_qty >= $record->qunatity
                        ? 'success'
                        : 'primary'
                )
                // ->visible(fn ($record) =>
                //     $record->posted_qty < $record->qunatity
                // )
                ->form([
                    TextInput::make('qty')
                        ->label('Quantity to Post')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(fn ($record) =>
                            $record->qunatity - $record->posted_qty
                        ),
                ])
                ->action(function ($record, array $data) {
                      $remaining = $record->qunatity - $record->posted_qty;

                    if ($data['qty'] > $remaining) {
                        throw ValidationException::withMessages([
                            'qty' => "Only {$remaining} quantity remaining.",
                        ]);
                    }
                    app(PostStockItem::class)
                        ->handle($record, $data['qty']);
                }),
        ];
    }
}
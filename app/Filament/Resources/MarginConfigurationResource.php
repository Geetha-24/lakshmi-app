<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarginConfigurationResource\Pages;
use App\Filament\Resources\MarginConfigurationResource\RelationManagers;
use App\Models\MarginConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarginConfigurationResource extends Resource
{
    protected static ?string $model = MarginConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';

    protected static ?string $navigationGroup = 'Costing Rule';

    protected static ?string $navigationLabel = 'Margin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pv_id')->label('select Product')->required()
                ->relationship('ProductVariant','p_name')
                ->default(request()->get('pv_id')),
                Forms\Components\TextInput::make('profit_amount')->label('Profit Amount')
                ->required()->default(0),
                Forms\Components\Radio::make('status')
                ->options([
                    0 =>"Active",
                    1=>"InActive"
                ])->required()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ProductVariant.p_name')->label('Product Name'),
                Tables\Columns\TextColumn::make('profit_amount')->label('Profit')

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
            'index' => Pages\ListMarginConfigurations::route('/'),
            'create' => Pages\CreateMarginConfiguration::route('/create'),
            'edit' => Pages\EditMarginConfiguration::route('/{record}/edit'),
        ];
    }    
}

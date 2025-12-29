<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackingTypeResource\Pages;
use App\Filament\Resources\PackingTypeResource\RelationManagers;
use App\Models\PackingType;
use App\Models\PackingTypes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackingTypeResource extends Resource
{
    protected static ?string $model = PackingType::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $navigationLabel = 'Packing Types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Packing Types')->required()->unique(),
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
                Tables\Columns\TextColumn::make('name')->label('Packing Types'),
                Tables\Columns\TextColumn::make('status')->label('Status')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'Active' : 'Inactive'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->action(function ($record) {
                    $record->status = 1;
                    $record->save();

                    $record->delete(); // soft delete
                    })
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
            'index' => Pages\ListPackingTypes::route('/'),
            'create' => Pages\CreatePackingType::route('/create'),
            'edit' => Pages\EditPackingType::route('/{record}/edit'),
        ];
    }
}

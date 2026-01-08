<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $navigationLabel = 'Brand';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Brand')
                    ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual: true),
               Tables\Columns\TextColumn::make('status')->label('Status')
                ->formatStateUsing(fn ($state) => (string) $state === '0' ? 'Active' : 'Inactive'),

            ])
            // ->filters([
            //     SelectFilter::make('sort_by')
            //         ->label('Sort By')
            //         ->options([
            //             'latest' => 'Latest',
            //             'name'   => 'Brand Name (A → Z)',
            //         ])
            //         ->query(function (Builder $query, array $data) {

            //             $query = match ($data['value'] ?? null) {
            //                 'latest' => $query->orderBy('created_at', 'desc'),
            //                 'name'   => $query->orderBy('name'),
            //                 default  => $query,
            //             };
            //             logger()->info($data['value']);

            //             logger()->info($query->toSql());

            //             return $query;
            //         })
            // ])
            ->filters([
            SelectFilter::make('sort_by')
                ->label('Sort By')
                ->options([
                    'latest' => 'Latest',
                    'name'   => 'Brand Name (A → Z)',
                ])
                ->query(fn (Builder $query) => $query), // 🔥 stop WHERE clause
        ])
        ->modifyQueryUsing(function (Builder $query, $livewire) {

            $sortBy = data_get($livewire, 'tableFilters.sort_by.value');
            logger()->info($query->toSql());
            if ($sortBy === 'latest') {
                $query->reorder('created_at', 'desc');
            }

            if ($sortBy === 'name') {
                $query->reorder('name', 'asc');
            }
            logger()->info($query->toSql());
        })
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}

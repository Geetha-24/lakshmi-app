<?php

namespace App\Filament\Resources\PackingTypeResource\Pages;

use App\Filament\Resources\PackingTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackingTypes extends ListRecords
{
    protected static string $resource = PackingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

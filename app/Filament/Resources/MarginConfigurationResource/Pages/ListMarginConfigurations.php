<?php

namespace App\Filament\Resources\MarginConfigurationResource\Pages;

use App\Filament\Resources\MarginConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarginConfigurations extends ListRecords
{
    protected static string $resource = MarginConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

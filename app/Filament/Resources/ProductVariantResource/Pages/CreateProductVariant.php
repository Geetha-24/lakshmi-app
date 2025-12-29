<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;


class CreateProductVariant extends CreateRecord
{
    protected static string $resource = ProductVariantResource::class;
    

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Product Variant Created')
            ->body('Do you want to setup margin now?')
            ->success()
            ->actions([
                Action::make('setupMargin')
                    ->label('Setup Margin')
                    ->url(
                        route(
                            'filament.admin.resources.margin-configurations.create',
                            ['pv_id' => $this->record->id]
                        )
                    ),
            ])
            ->send();
    }
}

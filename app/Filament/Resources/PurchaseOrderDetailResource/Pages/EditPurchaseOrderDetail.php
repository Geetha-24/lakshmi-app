<?php

namespace App\Filament\Resources\PurchaseOrderDetailResource\Pages;

use App\Filament\Resources\PurchaseOrderDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;


class EditPurchaseOrderDetail extends EditRecord
{
    protected static string $resource = PurchaseOrderDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function authorizeAccess(): void
        {
            if ($this->record->posted_qty > 0) {
                Notification::make()
                    ->title('Editing not allowed')
                    ->body('Stock has already been posted for this item.')
                    ->send();

                $this->redirect(
                    PurchaseOrderDetailResource::getUrl('index')
                );
            }
        }
      protected function mutateFormDataBeforeSave(array $data): array
        {
            if (! ($data['apply_tax'] ?? false)) {
                $data['tax_percentage'] = 0;
                $data['tax_amount'] = 0;
                $data['inc_tax_total_amount'] = $data['total_amount'];
            }

            return $data;
        }
}

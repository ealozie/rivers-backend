<?php

namespace App\Filament\Resources\TicketBulkVendingResource\Pages;

use App\Filament\Resources\TicketBulkVendingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketBulkVending extends EditRecord
{
    protected static string $resource = TicketBulkVendingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

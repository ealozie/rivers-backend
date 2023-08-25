<?php

namespace App\Filament\Resources\TicketBulkVendingResource\Pages;

use App\Filament\Resources\TicketBulkVendingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketBulkVendings extends ListRecords
{
    protected static string $resource = TicketBulkVendingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

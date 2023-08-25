<?php

namespace App\Filament\Resources\TicketVendingResource\Pages;

use App\Filament\Resources\TicketVendingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketVending extends EditRecord
{
    protected static string $resource = TicketVendingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

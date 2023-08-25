<?php

namespace App\Filament\Resources\TicketEnforcementResource\Pages;

use App\Filament\Resources\TicketEnforcementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketEnforcements extends ListRecords
{
    protected static string $resource = TicketEnforcementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\TicketAgentResource\Pages;

use App\Filament\Resources\TicketAgentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketAgents extends ListRecords
{
    protected static string $resource = TicketAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

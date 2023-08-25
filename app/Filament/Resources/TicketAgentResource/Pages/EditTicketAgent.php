<?php

namespace App\Filament\Resources\TicketAgentResource\Pages;

use App\Filament\Resources\TicketAgentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketAgent extends EditRecord
{
    protected static string $resource = TicketAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

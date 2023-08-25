<?php

namespace App\Filament\Resources\TicketAgentWalletResource\Pages;

use App\Filament\Resources\TicketAgentWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketAgentWallet extends EditRecord
{
    protected static string $resource = TicketAgentWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

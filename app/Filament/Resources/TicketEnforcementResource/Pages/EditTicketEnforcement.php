<?php

namespace App\Filament\Resources\TicketEnforcementResource\Pages;

use App\Filament\Resources\TicketEnforcementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketEnforcement extends EditRecord
{
    protected static string $resource = TicketEnforcementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

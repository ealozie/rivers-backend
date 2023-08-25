<?php

namespace App\Filament\Resources\TicketVendingResource\Pages;

use App\Filament\Resources\TicketVendingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicketVending extends CreateRecord
{
    protected static string $resource = TicketVendingResource::class;
}

<?php

namespace App\Filament\Resources\TicketAgentCategoryResource\Pages;

use App\Filament\Resources\TicketAgentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketAgentCategories extends ListRecords
{
    protected static string $resource = TicketAgentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

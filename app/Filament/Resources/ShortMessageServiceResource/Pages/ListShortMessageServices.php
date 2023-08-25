<?php

namespace App\Filament\Resources\ShortMessageServiceResource\Pages;

use App\Filament\Resources\ShortMessageServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShortMessageServices extends ListRecords
{
    protected static string $resource = ShortMessageServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

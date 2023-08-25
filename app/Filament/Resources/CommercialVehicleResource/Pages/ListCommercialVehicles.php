<?php

namespace App\Filament\Resources\CommercialVehicleResource\Pages;

use App\Filament\Resources\CommercialVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommercialVehicles extends ListRecords
{
    protected static string $resource = CommercialVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

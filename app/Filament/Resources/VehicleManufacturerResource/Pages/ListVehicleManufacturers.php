<?php

namespace App\Filament\Resources\VehicleManufacturerResource\Pages;

use App\Filament\Resources\VehicleManufacturerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleManufacturers extends ListRecords
{
    protected static string $resource = VehicleManufacturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

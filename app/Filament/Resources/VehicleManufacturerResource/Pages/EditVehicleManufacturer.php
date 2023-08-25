<?php

namespace App\Filament\Resources\VehicleManufacturerResource\Pages;

use App\Filament\Resources\VehicleManufacturerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleManufacturer extends EditRecord
{
    protected static string $resource = VehicleManufacturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

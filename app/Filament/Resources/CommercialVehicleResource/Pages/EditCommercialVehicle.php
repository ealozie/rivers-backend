<?php

namespace App\Filament\Resources\CommercialVehicleResource\Pages;

use App\Filament\Resources\CommercialVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommercialVehicle extends EditRecord
{
    protected static string $resource = CommercialVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

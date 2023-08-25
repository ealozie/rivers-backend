<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        $user->syncRoles($user->role);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['password'] = bcrypt($data['password']);

        return $data;
    }
}

<?php

namespace App\Filament\Resources\Plaza\PlazaUserResource\Pages;

use App\Filament\Resources\Plaza\PlazaUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaUser extends EditRecord
{
    protected static string $resource = PlazaUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}


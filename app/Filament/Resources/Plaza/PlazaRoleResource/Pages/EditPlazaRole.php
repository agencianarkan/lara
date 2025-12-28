<?php

namespace App\Filament\Resources\Plaza\PlazaRoleResource\Pages;

use App\Filament\Resources\Plaza\PlazaRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaRole extends EditRecord
{
    protected static string $resource = PlazaRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}


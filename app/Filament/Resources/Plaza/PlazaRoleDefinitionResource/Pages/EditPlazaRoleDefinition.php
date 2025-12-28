<?php

namespace App\Filament\Resources\Plaza\PlazaRoleDefinitionResource\Pages;

use App\Filament\Resources\Plaza\PlazaRoleDefinitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaRoleDefinition extends EditRecord
{
    protected static string $resource = PlazaRoleDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}


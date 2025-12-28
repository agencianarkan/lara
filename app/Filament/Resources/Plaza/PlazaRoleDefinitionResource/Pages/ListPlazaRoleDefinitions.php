<?php

namespace App\Filament\Resources\Plaza\PlazaRoleDefinitionResource\Pages;

use App\Filament\Resources\Plaza\PlazaRoleDefinitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaRoleDefinitions extends ListRecords
{
    protected static string $resource = PlazaRoleDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


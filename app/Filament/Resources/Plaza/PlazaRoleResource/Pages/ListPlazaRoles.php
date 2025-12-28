<?php

namespace App\Filament\Resources\Plaza\PlazaRoleResource\Pages;

use App\Filament\Resources\Plaza\PlazaRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaRoles extends ListRecords
{
    protected static string $resource = PlazaRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


<?php

namespace App\Filament\Resources\Plaza\PlazaTeamResource\Pages;

use App\Filament\Resources\Plaza\PlazaTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaTeams extends ListRecords
{
    protected static string $resource = PlazaTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


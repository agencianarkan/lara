<?php

namespace App\Filament\Resources\Plaza\PlazaMembershipTeamResource\Pages;

use App\Filament\Resources\Plaza\PlazaMembershipTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaMembershipTeams extends ListRecords
{
    protected static string $resource = PlazaMembershipTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


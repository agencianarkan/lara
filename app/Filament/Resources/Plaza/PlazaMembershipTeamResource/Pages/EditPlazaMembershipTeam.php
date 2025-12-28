<?php

namespace App\Filament\Resources\Plaza\PlazaMembershipTeamResource\Pages;

use App\Filament\Resources\Plaza\PlazaMembershipTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaMembershipTeam extends EditRecord
{
    protected static string $resource = PlazaMembershipTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}


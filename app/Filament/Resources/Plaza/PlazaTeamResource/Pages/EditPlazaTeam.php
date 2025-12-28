<?php

namespace App\Filament\Resources\Plaza\PlazaTeamResource\Pages;

use App\Filament\Resources\Plaza\PlazaTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaTeam extends EditRecord
{
    protected static string $resource = PlazaTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}


<?php

namespace App\Filament\Resources\Plaza\PlazaCustomOverrideResource\Pages;

use App\Filament\Resources\Plaza\PlazaCustomOverrideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaCustomOverrides extends ListRecords
{
    protected static string $resource = PlazaCustomOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


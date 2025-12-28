<?php

namespace App\Filament\Resources\Plaza\PlazaCapabilityResource\Pages;

use App\Filament\Resources\Plaza\PlazaCapabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaCapabilities extends ListRecords
{
    protected static string $resource = PlazaCapabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


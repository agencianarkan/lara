<?php

namespace App\Filament\Resources\Plaza\PlazaUserResource\Pages;

use App\Filament\Resources\Plaza\PlazaUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaUsers extends ListRecords
{
    protected static string $resource = PlazaUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


<?php

namespace App\Filament\Resources\Plaza\PlazaStoreResource\Pages;

use App\Filament\Resources\Plaza\PlazaStoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaStores extends ListRecords
{
    protected static string $resource = PlazaStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


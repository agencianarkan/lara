<?php

namespace App\Filament\Resources\Plaza\PlazaMembershipResource\Pages;

use App\Filament\Resources\Plaza\PlazaMembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlazaMemberships extends ListRecords
{
    protected static string $resource = PlazaMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


<?php

namespace App\Filament\Resources\Plaza\PlazaStoreResource\Pages;

use App\Filament\Resources\Plaza\PlazaStoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaStore extends EditRecord
{
    protected static string $resource = PlazaStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}


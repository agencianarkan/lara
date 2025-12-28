<?php

namespace App\Filament\Resources\Plaza\PlazaCapabilityResource\Pages;

use App\Filament\Resources\Plaza\PlazaCapabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaCapability extends EditRecord
{
    protected static string $resource = PlazaCapabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}


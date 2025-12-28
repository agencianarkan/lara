<?php

namespace App\Filament\Resources\Plaza\PlazaMembershipResource\Pages;

use App\Filament\Resources\Plaza\PlazaMembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlazaMembership extends EditRecord
{
    protected static string $resource = PlazaMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}


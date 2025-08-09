<?php

namespace App\Filament\Site\Resources\AwarenessResource\Pages;

use App\Filament\Site\Resources\AwarenessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwareness extends EditRecord
{
    protected static string $resource = AwarenessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

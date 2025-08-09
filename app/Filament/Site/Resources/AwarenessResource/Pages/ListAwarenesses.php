<?php

namespace App\Filament\Site\Resources\AwarenessResource\Pages;

use App\Filament\Site\Resources\AwarenessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAwarenesses extends ListRecords
{
    protected static string $resource = AwarenessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

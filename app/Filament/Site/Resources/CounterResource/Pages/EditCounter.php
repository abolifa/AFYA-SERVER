<?php

namespace App\Filament\Site\Resources\CounterResource\Pages;

use App\Filament\Site\Resources\CounterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCounter extends EditRecord
{
    protected static string $resource = CounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

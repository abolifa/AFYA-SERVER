<?php

namespace App\Filament\Site\Resources\CounterResource\Pages;

use App\Filament\Site\Resources\CounterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCounter extends CreateRecord
{
    protected static string $resource = CounterResource::class;
}

<?php

namespace App\Filament\Site\Resources\ComplaintResource\Pages;

use App\Filament\Site\Resources\ComplaintResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewComplaint extends ViewRecord
{
    protected static string $resource = ComplaintResource::class;


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make([
                    TextEntry::make('name')
                        ->label('الإسم'),
                    TextEntry::make('phone')
                        ->label('رقم الهاتف'),
                    TextEntry::make('message')
                        ->columnSpanFull()
                        ->label('الرسالة'),
                ])->columns(),
            ]);
    }
}

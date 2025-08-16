<?php

namespace App\Filament\Site\Resources\ComplaintResource\Pages;

use App\Filament\Site\Resources\ComplaintResource;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewComplaint extends ViewRecord
{
    protected static string $resource = ComplaintResource::class;


    public function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make([
                    TextEntry::make('name')
                        ->label('الإسم')
                        ->placeholder('-')
                        ->weight(FontWeight::Bold)
                        ->size(TextEntry\TextEntrySize::Large),
                    TextEntry::make('phone')
                        ->label('رقم الهاتف')
                        ->placeholder('-')
                        ->weight(FontWeight::Bold)
                        ->size(TextEntry\TextEntrySize::Large),
                    TextEntry::make('message')
                        ->label('الرسالة')
                        ->columnSpanFull()
                        ->size(TextEntry\TextEntrySize::Medium)
                        ->weight(FontWeight::SemiBold),
                ])->columns(),
            ]);
    }
}

<?php

namespace App\Support;

use Filament\Tables\Columns\TextColumn;

class SharedTableColumns
{
    /**
     * Plain array of blame columns.
     */
    public static function blame(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('تاريخ الإنشاء')
                ->badge()
                ->color('gray')
                ->placeholder('-')
                ->alignCenter()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->label('تاريخ التحديث')
                ->badge()
                ->color('gray')
                ->placeholder('-')
                ->alignCenter()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('deleted_at')
                ->label('تاريخ الحذف')
                ->badge()
                ->color('gray')
                ->placeholder('-')
                ->alignCenter()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('creator.name')
                ->label('أنشئ بواسطة')
                ->badge()
                ->color('gray')
                ->placeholder('-')
                ->alignCenter()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updater.name')
                ->label('التحديث بواسطة')
                ->badge()
                ->color('gray')
                ->placeholder('-')
                ->alignCenter()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('deleter.name')
                ->label('الحذف بواسطة')
                ->badge()
                ->color('gray')
                ->placeholder('-')
                ->alignCenter()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}

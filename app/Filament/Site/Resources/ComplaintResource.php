<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\ComplaintResource\Pages;
use App\Models\Complaint;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسم')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->placeholder('-')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('الشكوى')
                    ->placeholder('-')
                    ->searchable()
                    ->alignCenter()
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الشكوى')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'view' => Pages\ViewComplaint::route('/{record}'),
        ];
    }
}

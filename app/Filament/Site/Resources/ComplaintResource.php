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

    protected static ?string $navigationIcon = 'gmdi-message-o';

    protected static ?string $pluralLabel = 'الشكاوى';
    protected static ?string $label = 'شكوى';


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->placeholder('غير معروف'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->placeholder('غير معروف')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('message')
                    ->label('الرسالة')
                    ->limit(50)
                    ->alignCenter()
                    ->placeholder('لا توجد رسالة'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->alignCenter()
                    ->date('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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

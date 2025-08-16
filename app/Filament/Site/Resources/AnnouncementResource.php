<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\AnnouncementResource\Pages;
use App\Forms\Components\BooleanField;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'gmdi-bolt-o';

    protected static ?string $label = 'إعلان';
    protected static ?string $pluralLabel = 'الإعلانات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('content')
                    ->label('المحتوى')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->label('الصور')
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->directory('announcements')
                    ->disk('public')
                    ->columnSpanFull(),
                BooleanField::make('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('images')
                    ->alignCenter()
                    ->limit()
                    ->square()
                    ->label('الصور'),
                Tables\Columns\ToggleColumn::make('active')
                    ->alignCenter()
                    ->label('نشط'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}

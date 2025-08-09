<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\SliderResource\Pages;
use App\Filament\Site\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('type')
                        ->label('نوع الشريحة')
                        ->options([
                            'image' => 'صورة',
                            'url' => 'رابط',
                            'page' => 'صفحة',
                            'post' => 'مقالة',
                        ])
                        ->required(),
                    Forms\Components\Select::make('post_id')
                        ->label('المقالة')
                        ->relationship('post', 'title')
                        ->searchable()
                        ->preload(),
                    Forms\Components\FileUpload::make('image')
                        ->label('الصورة')
                        ->disk('public')
                        ->directory('sliders')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\TextInput::make('url')
                        ->label('الرابط')
                        ->nullable()
                        ->maxLength(255),
                    Forms\Components\ToggleButtons::make('active')
                        ->label('نشط')
                        ->boolean()
                        ->inline()
                        ->grouped()
                        ->default(true)
                        ->required(),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة'),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الشريحة')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'image' => 'صورة',
                        'url' => 'رابط',
                        'page' => 'صفحة',
                        'post' => 'مقالة',
                        default => 'غير معروف',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'image' => 'success',
                        'url' => 'warning',
                        'page' => 'info',
                        'post' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('post.title')
                    ->label('المقالة')
                    ->placeholder('-')
                    ->alignCenter()
                    ->limit(20)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('active')
                    ->alignCenter()
                    ->label('نشط'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}

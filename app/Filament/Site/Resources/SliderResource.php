<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\SliderResource\Pages;
use App\Forms\Components\BooleanField;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $label = 'شريحة';
    protected static ?string $pluralLabel = 'الشرائح';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\ToggleButtons::make('type')
                    ->label('نوع الشريحة')
                    ->options([
                        'image' => 'صورة',
                        'url' => 'رابط',
                        'post' => 'منشور',
                    ])
                    ->default('image')
                    ->inline()
                    ->grouped()
                    ->reactive()
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('صورة الشريحة')
                    ->disk('public')
                    ->directory('sliders')
                    ->visibility('public')
                    ->imageEditor()
                    ->image()
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Select::make('post_id')
                    ->label('المنشور المرتبط')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->disabled(fn($get) => $get('type') !== 'post')
                    ->required(fn($get) => $get('type') === 'post')
                    ->relationship('post', 'title'),
                Forms\Components\TextInput::make('url')
                    ->label('رابط الشريحة')
                    ->disabled(fn($get) => $get('type') !== 'url')
                    ->required(fn($get) => $get('type') === 'url')
                    ->url()
                    ->placeholder('https://example.com')
                    ->maxLength(255),
                BooleanField::make('active')
                    ->label('نشط'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الشريحة')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'image' => 'صورة',
                        'url' => 'رابط',
                        'post' => 'منشور',
                        default => 'غير معروف',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'image' => 'success',
                        'url' => 'warning',
                        'post' => 'info',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('post.title')
                    ->label('المنشور')
                    ->placeholder('-')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->placeholder('-')
                    ->alignCenter(),
                Tables\Columns\ToggleColumn::make('active')
                    ->label('نشط')
                    ->alignCenter(),
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

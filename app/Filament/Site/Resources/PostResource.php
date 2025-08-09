<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\PostResource\Pages;
use App\Filament\Site\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('title')
                        ->label('عنوان المقالة')
                        ->columnSpanFull()
                        ->required()
                        ->reactive()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                            $slug = str($state)->slug();
                            $set('slug', $slug);
                        })
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->label('الرابط الثابت')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TagsInput::make('tags')
                        ->label('الوسوم')
                        ->placeholder('أضف وسومًا للمقالة #ليبيا #تكنولوجيا'),
                    Forms\Components\RichEditor::make('content')
                        ->label('المحتوى')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('main_image')
                        ->label('الصورة الرئيسية')
                        ->disk('public')
                        ->directory('posts')
                        ->imageEditor()
                        ->columnSpanFull()
                        ->image(),
                    Forms\Components\ToggleButtons::make('is_published')
                        ->label('نشر المقالة')
                        ->boolean()
                        ->default(true)
                        ->inline()
                        ->grouped()
                        ->required(),
                    Forms\Components\FileUpload::make('images')
                        ->label('صور إضافية')
                        ->image()
                        ->multiple()
                        ->disk('public')
                        ->directory('posts')
                        ->maxFiles(5)
                        ->columnSpanFull(),
                ])->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('الصورة الرئيسية'),
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->alignCenter()
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->alignCenter()
                    ->label('نشر'),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

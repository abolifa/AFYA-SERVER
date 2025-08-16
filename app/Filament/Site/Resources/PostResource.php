<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\PostResource\Pages;
use App\Forms\Components\BooleanField;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'gmdi-post-add';

    protected static ?string $label = 'منشور';
    protected static ?string $pluralLabel = 'المنشورات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->reactive()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, Forms\Set $set, $state) {
                        if ($operation !== 'create') {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    })
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('الرابط')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('tags')
                    ->helperText('#ليبيا #تونس #مصر')
                    ->label('الوسوم'),
                BooleanField::make('active'),
                Forms\Components\FileUpload::make('main_image')
                    ->label('الصورة الرئيسية')
                    ->directory('posts')
                    ->disk('public')
                    ->imageEditor()
                    ->columnSpanFull()
                    ->image(),
                Forms\Components\FileUpload::make('other_images')
                    ->label('صور إضافية')
                    ->directory('posts')
                    ->disk('public')
                    ->multiple()
                    ->columnSpanFull()
                    ->imageEditor()
                    ->image(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('الصورة الرئيسية')
                    ->square()
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->alignCenter()
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('active')
                    ->label('نشط')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

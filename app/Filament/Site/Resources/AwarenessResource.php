<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\AwarenessResource\Pages;
use App\Forms\Components\BooleanField;
use App\Models\Awareness;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

class AwarenessResource extends Resource
{
    protected static ?string $model = Awareness::class;

    protected static ?string $navigationIcon = 'gmdi-follow-the-signs';

    protected static ?string $label = 'توعية';
    protected static ?string $pluralLabel = 'التوعيات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->label('المحتوى')
                    ->columnSpanFull(),
                BooleanField::make('active'),
                Forms\Components\Repeater::make('attachments')
                    ->label('المرفقات')
                    ->defaultItems(1)
                    ->addActionLabel('إضافة مرفق')
                    ->reorderable()
                    ->collapsible()
                    ->collapsed(false)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان المرفق'),
                        Forms\Components\TextInput::make('content')
                            ->label('محتوى المرفق'),
                        Forms\Components\FileUpload::make('image')
                            ->label('صورة المرفق')
                            ->image()
                            ->imageEditor()
                            ->directory('awareness')
                            ->disk('public'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->label('عدد المرفقات')
                    ->getStateUsing(function ($record) {
                        $items = collect($record->attachments ?? []);
                        return $items->filter(function ($row) {
                            if (!is_array($row)) return false;
                            $title = trim((string)Arr::get($row, 'title', ''));
                            $content = trim((string)Arr::get($row, 'content', ''));
                            $image = (string)Arr::get($row, 'image', '');
                            return $title !== '' || $content !== '' || $image !== '';
                        })->count();
                    })
                    ->alignCenter()
                    ->sortable()
                    ->badge(),
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
            'index' => Pages\ListAwarenesses::route('/'),
            'create' => Pages\CreateAwareness::route('/create'),
            'edit' => Pages\EditAwareness::route('/{record}/edit'),
        ];
    }
}

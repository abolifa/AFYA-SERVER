<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\AwarenessResource\Pages;
use App\Models\Awareness;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AwarenessResource extends Resource
{
    protected static ?string $model = Awareness::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->columnSpanFull()
                        ->maxLength(255),
                    Forms\Components\RichEditor::make('description')
                        ->label('الوصف')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('attachments')
                        ->label('المرفقات')
                        ->maxItems(20)
                        ->defaultItems(1)
                        ->addActionLabel('إضافة مرفق')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('العنوان')
                                ->maxLength(255),
                            Forms\Components\FileUpload::make('image')
                                ->label('الصورة')
                                ->image()
                                ->imageEditor(),
                        ])
                        ->columnSpanFull(),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAwarenesses::route('/'),
            'create' => Pages\CreateAwareness::route('/create'),
            'edit' => Pages\EditAwareness::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\StructureResource\Pages;
use App\Models\Structure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StructureResource extends Resource
{
    protected static ?string $model = Structure::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'نقطة';
    protected static ?string $pluralLabel = 'الهيكل التنظيمي';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('الهيكل الأب')
                    ->native(false)
                    ->relationship('parent', 'name'),
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('النوع')
                    ->native(false)
                    ->required()
                    ->options([
                        'authority' => 'هيئة',
                        'directorate' => 'إدارة',
                        'department' => 'قسم',
                        'division' => 'شعبة',
                        'unit' => 'وحدة',
                        'center' => 'مركز',
                        'office' => 'مكتب',
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->label('الهاتف')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->label('العنوان')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('الهيكل الأب')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->formatState(fn(string $state) => match ($state) {
                        'authority' => 'هيئة',
                        'directorate' => 'إدارة',
                        'department' => 'قسم',
                        'division' => 'شعبة',
                        'unit' => 'وحدة',
                        'center' => 'مركز',
                        'office' => 'مكتب',
                    })
                    ->badge()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->alignCenter()
                    ->searchable(),
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
            'index' => Pages\ListStructures::route('/'),
            'create' => Pages\CreateStructure::route('/create'),
            'edit' => Pages\EditStructure::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Forms\Components\BooleanField;
use App\Forms\Components\Selector;
use App\Models\User;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'fas-users';

    protected static ?string $label = "حساب";
    protected static ?string $pluralLabel = "الحسابات";

    protected static ?string $navigationGroup = "إدارة الموارد";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('الإسم')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->live()
                        ->unique(ignoreRecord: true)
                        ->validationMessages([
                            'regex' => 'ضيغة البريد الإلكتروني غير صحيحة',
                            'unique' => 'البريد الإلكتروني مسجل مسبقاً',
                        ])
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->maxLength(10)
                        ->minLength(10)
                        ->validationMessages([
                            'regex' => 'ضيغة رقم الهاتف غير صحيحة',
                            'unique' => 'رقم الهاتف مسجل مسبقاً',
                        ])
                        ->live()
                        ->unique(ignoreRecord: true)
                        ->rule('regex:/^09[1-5][0-9]{7}$/'),
                    Forms\Components\TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->revealable()
                        ->required(fn(string $context) => $context === 'create')
                        ->disabled(fn(string $context) => $context === 'edit')
                        ->maxLength(255),
                    Selector::make('center_id')
                        ->label('المركز')
                        ->relationship('center', 'name'),

                    Forms\Components\Select::make('roles')
                        ->label('الصلاحيات')
                        ->relationship('roles', 'name')
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('account_type')
                        ->label('نوع المستخدم')
                        ->options([
                            'admin' => 'مدير',
                            'doctor' => 'طبيب',
                            'stock' => 'مخزن',
                            'pharmacy' => 'صيدلية',
                            'user' => 'مستخدم',
                        ])
                        ->native(false)
                        ->default('user')
                        ->required()
                        ->reactive(),
                    BooleanField::make('is_active'),
                ])->columns(),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسم')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_type')
                    ->label('نوع المستخدم')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'admin' => 'مدير',
                        'doctor' => 'طبيب',
                        'stock' => 'مخزن',
                        'pharmacy' => 'صيدلية',
                        'user' => 'مستخدم',
                        default => 'غير محدد',
                    })
                    ->color(fn($state) => match ($state) {
                        'admin' => 'danger',
                        'doctor' => 'primary',
                        'stock' => 'warning',
                        'pharmacy' => 'success',
                        'user' => 'info',
                        default => 'gray',
                    })
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->alignCenter()
                    ->sortable()
                    ->label('نشط'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('الصلاحيات')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'super_admin' => 'مدير النظام',
                        default => $state,
                    })
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('account_type')
                    ->label('نوع المستخدم')
                    ->options([
                        'admin' => 'مدير',
                        'doctor' => 'طبيب',
                        'stock' => 'مخزن',
                        'pharmacy' => 'صيدلية',
                        'user' => 'مستخدم',
                    ]),
                Tables\Filters\SelectFilter::make('center_id')
                    ->label('المركز')
                    ->relationship('center', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

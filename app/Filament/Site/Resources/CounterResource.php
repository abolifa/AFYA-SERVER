<?php

namespace App\Filament\Site\Resources;

use App\Filament\Site\Resources\CounterResource\Pages;
use App\Models\Counter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CounterResource extends Resource
{
    protected static ?string $model = Counter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        $iconOptions = [
            // Users / People
            'User' => 'fas-user',
            'Users' => 'fas-users',
            'UserPlus' => 'fas-user-plus',
            'UserCheck' => 'fas-user-check',
            'UserCircle' => 'fas-user-circle',

            // Finance / Sales
            'DollarSign' => 'fas-dollar-sign',
            'CreditCard' => 'fas-credit-card',
            'ShoppingCart' => 'fas-shopping-cart',
            'Package' => 'fas-box',
            'Wallet' => 'fas-wallet',

            // Documents / Files
            'File' => 'fas-file',
            'FileText' => 'fas-file-alt',
            'Folder' => 'fas-folder',
            'Inbox' => 'fas-inbox',
            'ClipboardList' => 'fas-clipboard-list',

            // Inventory / Products
            'Box' => 'fas-box-open',
            'Tags' => 'fas-tags',
            'Barcode' => 'fas-barcode',
            'Layers' => 'fas-layer-group',

            // Analytics / Growth
            'BarChart' => 'fas-chart-bar',
            'PieChart' => 'fas-chart-pie',
            'LineChart' => 'fas-chart-line',
            'TrendingUp' => 'fas-arrow-up',
            'TrendingDown' => 'fas-arrow-down',

            // Time / Events
            'Calendar' => 'fas-calendar-alt',
            'Clock' => 'fas-clock',
            'Timer' => 'fas-stopwatch',
            'AlarmClock' => 'fas-bell',
            'History' => 'fas-history',

            // Settings / Operations
            'Settings' => 'fas-cog',
            'SlidersHorizontal' => 'fas-sliders-h',
            'Wrench' => 'fas-wrench',
            'Cog' => 'fas-cog',
            'Hammer' => 'fas-hammer',

            // Communication
            'Mail' => 'fas-envelope',
            'MessageSquare' => 'fas-comment-dots',
            'Phone' => 'fas-phone',
            'Send' => 'fas-paper-plane',
            'Bell' => 'fas-bell',

            // Status / Confirmation
            'CheckCircle' => 'fas-check-circle',
            'CircleCheck' => 'fas-check-circle',
            'AlertCircle' => 'fas-exclamation-circle',
            'XCircle' => 'fas-times-circle',
            'Shield' => 'fas-shield-alt',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slogan')
                        ->label('الوصف')
                        ->maxLength(255),
                    Forms\Components\ColorPicker::make('color')
                        ->label('اللون')
                        ->default('#000000')
                        ->required(),
                    Forms\Components\Select::make('icon')
                        ->label('الأيقونة')
                        ->options($iconOptions)
                        ->getOptionLabelUsing(fn($value) => view('components.icon-option', [
                            'lucide' => $value,
                            'fa' => $iconOptions[$value] ?? 'fas fa-question-circle'
                        ])->render()),
                    Forms\Components\ToggleButtons::make('type')
                        ->label('النوع')
                        ->reactive()
                        ->inline()
                        ->columnSpanFull()
                        ->grouped()
                        ->afterStateUpdated(
                            function ($state, Forms\Set $set) {
                                if ($state === 'model_count') {
                                    $set('fixed_value', null);
                                } elseif ($state === 'fixed_amount') {
                                    $set('model', null);
                                }
                            }
                        )
                        ->options([
                            'model_count' => 'عداد نموذج',
                            'fixed_amount' => 'قيمة مخصصة',
                        ])
                        ->default('model_count')
                        ->required(),
                    Forms\Components\Select::make('model')
                        ->label('النموذج')
                        ->native(false)
                        ->options([
                            'App\Models\Product' => 'المنتجات',
                            'App\Models\Order' => 'الطلبات',
                            'App\Models\Center' => 'المراكز',
                            'App\Models\Appointment' => 'المواعيد',
                            'App\Models\Device' => 'الأجهزة',
                            'App\Models\Patient' => 'المرضى',
                            'App\Models\Doctor' => 'الأطباء',
                            'App\Models\Prescription' => 'الوصفات الطبية',
                        ])
                        ->required(fn(Forms\Get $get) => $get('type') === 'model_count')
                        ->disabled(fn(Forms\Get $get) => $get('type') !== 'model_count'),
                    Forms\Components\TextInput::make('fixed_value')
                        ->label('القيمة المخصصة')
                        ->required(fn(Forms\Get $get) => $get('type') === 'fixed_amount')
                        ->disabled(fn(Forms\Get $get) => $get('type') !== 'fixed_amount')
                        ->numeric(),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slogan')
                    ->label('الوصف')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color')
                    ->label('اللون')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fixed_value')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListCounters::route('/'),
            'create' => Pages\CreateCounter::route('/create'),
            'edit' => Pages\EditCounter::route('/{record}/edit'),
        ];
    }
}

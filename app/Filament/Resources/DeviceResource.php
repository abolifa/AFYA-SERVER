<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Forms\Components\BooleanField;
use App\Models\Appointment;
use App\Models\Device;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'fas-calendar-days';

    protected static ?string $label = "جهاز";
    protected static ?string $pluralLabel = "الأجهزة";

    protected static ?string $navigationGroup = "إدارة الموارد";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم الجهاز')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('manufacturer')
                        ->label('الشركة المصنعة')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('model')
                        ->label('الموديل')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('serial_number')
                        ->label('الرقم التسلسلي')
                        ->maxLength(255),
                    BooleanField::make('active')
                        ->default(true),
                ])->columns()
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الجهاز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manufacturer')
                    ->label('الشركة المصنعة')
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('الموديل')
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('الرقم التسلسلي')
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_usage')
                    ->label('إجمالي الاستخدام (دقائق)')
                    ->alignCenter()
                    ->placeholder('0')
                    ->getStateUsing(function (Device $record) {
                        $appointments = Appointment::where('device_id', $record->id)
                            ->where('status', 'completed')
                            ->get(['start_time', 'end_time']);
                        return $appointments->sum(function ($appointment) {
                            $start = Carbon::parse($appointment->start_time);
                            $end = Carbon::parse($appointment->end_time);
                            $time = abs($end->diffInMinutes($start));
                            return ($time);
                        });
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('active')
                    ->label('الحالة')
                    ->options([
                        '1' => 'نشط',
                        '0' => 'غير نشط',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListDevices::route('/'),
        ];
    }
}

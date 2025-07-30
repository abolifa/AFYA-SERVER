<?php

namespace App\Livewire;

use App\Models\Vital;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;

class OverviewVitalsTable extends TableComponent
{
    public int $patientId;

    public function table(Table $table): Table
    {
        return $table
            ->query(Vital::query()->where('patient_id', $this->patientId))
            ->heading('المؤشرات الحيوية')
            ->defaultSort('recorded_at', 'desc')
            ->emptyStateIcon('fas-heart-pulse')
            ->emptyStateHeading('لا توجد مؤشرات حيوية مسجلة')
            ->columns([
                Tables\Columns\TextColumn::make('recorded_at')
                    ->label('تاريخ التسجيل')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('weight')
                    ->label('الوزن')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn($state) => match (true) {
                        $state >= 50 && $state <= 100 => 'success',
                        $state > 100 && $state <= 120 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('blood_pressure')
                    ->label('ضغط الدم')
                    ->getStateUsing(fn(Vital $record): string => "$record->systolic/$record->diastolic")
                    ->alignCenter()
                    ->color(fn(string $state, Vital $record): string => match (true) {
                        $record->systolic < 120 && $record->diastolic < 80 => 'success',
                        $record->systolic < 140 && $record->diastolic < 90 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('heart_rate')
                    ->label('معدل ضربات القلب')
                    ->numeric()
                    ->alignCenter()
                    ->suffix(' p/m ')
                    ->color(fn($state) => match (true) {
                        $state >= 60 && $state <= 100 => 'success',
                        $state > 100 && $state <= 120 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('temperature')
                    ->label('درجة الحرارة')
                    ->numeric()
                    ->alignCenter()
                    ->suffix('°C')
                    ->color(fn($state) => match (true) {
                        $state >= 36 && $state <= 37.5 => 'success',
                        $state > 37.5 && $state <= 38.5 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('oxygen_saturation')
                    ->label('تشبع الأكسجين')
                    ->numeric()
                    ->alignCenter()
                    ->suffix('%')
                    ->color(fn($state) => match (true) {
                        $state >= 95 => 'success',
                        $state >= 90 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('sugar_level')
                    ->label('مستوى السكر')
                    ->numeric()
                    ->alignCenter()
                    ->suffix(' mg/dL')
                    ->color(fn($state) => match (true) {
                        $state >= 70 && $state <= 140 => 'success',
                        $state > 140 && $state <= 200 => 'warning',
                        default => 'danger',
                    }),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة مؤشر')
                    ->form([
                        Forms\Components\Section::make([
                            Forms\Components\DatePicker::make('recorded_at')
                                ->label('تاريخ التسجيل')
                                ->required()
                                ->displayFormat('d/m/Y')
                                ->default(now()),
                            Forms\Components\TextInput::make('weight')
                                ->label('الوزن')
                                ->numeric(),
                            Forms\Components\TextInput::make('systolic')
                                ->label('الضغط الانقباضي')
                                ->numeric(),
                            Forms\Components\TextInput::make('diastolic')
                                ->label('الضغط الانبساطي')
                                ->numeric(),
                            Forms\Components\TextInput::make('heart_rate')
                                ->label('معدل ضربات القلب')
                                ->numeric(),
                            Forms\Components\TextInput::make('temperature')
                                ->label('درجة الحرارة')
                                ->numeric(),
                            Forms\Components\TextInput::make('oxygen_saturation')
                                ->label('تشبع الأكسجين')
                                ->numeric(),
                            Forms\Components\TextInput::make('sugar_level')
                                ->label('مستوى السكر')
                                ->numeric(),
                            Forms\Components\Textarea::make('notes')
                                ->label('ملاحظات')
                                ->rows(3)
                                ->columnSpanFull()
                                ->maxLength(500),
                        ])->columns(4)
                    ])->mutateFormDataUsing(function (array $data): array {
                        $data['patient_id'] = $this->patientId;
                        return $data;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['patient_id'] = $this->patientId;
                        return $data;
                    })
                    ->form([
                        Forms\Components\Section::make([
                            Forms\Components\DatePicker::make('recorded_at')
                                ->label('تاريخ التسجيل')
                                ->required()
                                ->displayFormat('d/m/Y'),
                            Forms\Components\TextInput::make('weight')
                                ->label('الوزن')
                                ->numeric(),
                            Forms\Components\TextInput::make('systolic')
                                ->label('الضغط الانقباضي')
                                ->numeric(),
                            Forms\Components\TextInput::make('diastolic')
                                ->label('الضغط الانبساطي')
                                ->numeric(),
                            Forms\Components\TextInput::make('heart_rate')
                                ->label('معدل ضربات القلب')
                                ->numeric(),
                            Forms\Components\TextInput::make('temperature')
                                ->label('درجة الحرارة')
                                ->numeric(),
                            Forms\Components\TextInput::make('oxygen_saturation')
                                ->label('تشبع الأكسجين')
                                ->numeric(),
                            Forms\Components\TextInput::make('sugar_level')
                                ->label('مستوى السكر')
                                ->numeric(),
                            Forms\Components\Textarea::make('notes')
                                ->label('ملاحظات')
                                ->rows(3)
                                ->columnSpanFull()
                                ->maxLength(500),
                        ])->columns(4),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPendingAppointments extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'أحدث المواعيد المعلقة';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::with(['patient', 'doctor', 'center', 'order'])->where('status', 'pending')
                    ->latest()
                    ->take(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('المريض')
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->badge()
                    ->alignCenter()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('الطبيب')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('time')
                    ->label('الوقت')
                    ->time('h:i A')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مؤكد',
                        'cancelled' => 'ملغي',
                        'completed' => 'مكتمل',
                        default => 'غير معروف',
                    })
                    ->alignCenter()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'primary',
                        default => 'secondary',
                    }),
                Tables\Columns\IconColumn::make('intended')
                    ->label('الحضور')
                    ->alignCenter()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->emptyStateHeading('لا توجد مواعيد معلقة')
            ->paginated(false)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('confirm')
                        ->label('تأكيد')
                        ->icon('fas-check-circle')
                        ->action(function (Appointment $record) {
                            $record->update(['status' => 'confirmed']);
                            if ($record->order) {
                                $record->order->update(['status' => 'confirmed']);
                            }
                        })
                        ->color('success')
                        ->visible(fn(Appointment $record) => $record->status === 'pending')
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('cancel')
                        ->label('إلغاء')
                        ->icon('fas-times-circle')
                        ->action(function (Appointment $record) {
                            $record->update(['status' => 'cancelled']);

                            if ($record->order) {
                                $record->order->update(['status' => 'cancelled']);
                            }
                        })
                        ->color('danger')
                        ->visible(fn(Appointment $record) => $record->status === 'pending')
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('reschedule')
                        ->label('إعادة جدولة')
                        ->icon('fas-calendar-alt')
                        ->form([
                            Forms\Components\DatePicker::make('date')
                                ->label('التاريخ')
                                ->displayFormat('d/m/Y')
                                ->default(fn(Appointment $record) => $record->date)
                                ->required(),
                            Forms\Components\TimePicker::make('time')
                                ->label('الوقت')
                                ->displayFormat('h:i A')
                                ->default(fn(Appointment $record) => $record->time)
                                ->required(),
                        ])
                        ->action(function (Appointment $record, array $data) {
                            $record->update([
                                'status' => 'pending',
                                'date' => Carbon::parse($data['date'])->format('Y-m-d'),
                                'time' => Carbon::parse($data['time'])->format('H:i:s'),
                            ]);
                        })
                        ->visible(fn(Appointment $record) => $record->status !== 'completed')
                        ->requiresConfirmation(false),
                    Tables\Actions\ViewAction::make(),
                ])
            ]);
    }
}

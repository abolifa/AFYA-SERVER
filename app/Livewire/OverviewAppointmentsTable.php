<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Support\SharedTableColumns;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;

class OverviewAppointmentsTable extends TableComponent
{
    public int $patientId;

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading('المواعيد')
            ->query(Appointment::query()->where('patient_id', $this->patientId))
            ->emptyStateHeading('لا توجد مواعيد')
            ->emptyStateIcon('fas-calendar-day')
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('المريض')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->badge()
                    ->searchable()
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('الطبيب')
                    ->alignCenter()
                    ->badge()
                    ->searchable()
                    ->color('info')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('الوقت')
                    ->time('h:i A')
                    ->alignCenter()
                    ->sortable(),
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
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('intended')
                    ->label('الحضور')
                    ->alignCenter()
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('time_spent')
                    ->label('المدة')
                    ->alignCenter()
                    ->alignCenter()
                    ->getStateUsing(function (Appointment $record) {
                        if ($record->start_time && $record->end_time) {
                            $start = Carbon::parse($record->start_time);
                            $end = Carbon::parse($record->end_time);
                            return $start->diffInMinutes($end);
                        }
                        return '-';
                    }),

                ...SharedTableColumns::blame(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('إضافة موعد')
                    ->url(fn() => route('filament.admin.resources.appointments.create')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(Appointment $record) => route('filament.admin.resources.appointments.view', $record)),
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
                    Tables\Actions\Action::make('complete')
                        ->label('إكمال')
                        ->icon('fas-check-double')
                        ->form([
                            Forms\Components\TimePicker::make('start_time')
                                ->label('وقت البداية')
                                ->displayFormat('h:i A')
                                ->default(fn(Appointment $record) => $record->start_time)
                                ->required(),
                            Forms\Components\TimePicker::make('end_time')
                                ->label('وقت النهاية')
                                ->displayFormat('h:i A')
                                ->default(fn(Appointment $record) => $record->end_time)
                                ->required(),
                        ])
                        ->action(fn(Appointment $record, array $data) => $record->update([
                            'status' => 'completed',
                            'intended' => true,
                            'start_time' => Carbon::parse($data['start_time'])->format('H:i:s'),
                            'end_time' => Carbon::parse($data['end_time'])->format('H:i:s'),
                        ]))
                        ->color('primary')
                        ->visible(fn(Appointment $record) => $record->status === 'confirmed')
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
                    Tables\Actions\Action::make('edit')
                        ->label('تعديل')
                        ->icon('heroicon-o-pencil')
                        ->url(fn(Appointment $record) => route('filament.admin.resources.appointments.edit', $record))
                        ->openUrlInNewTab(false),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ])
            ])
            ->bulkActions([]);
    }
}

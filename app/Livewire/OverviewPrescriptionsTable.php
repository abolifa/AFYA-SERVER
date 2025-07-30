<?php

namespace App\Livewire;

use App\Models\Prescription;
use App\Support\SharedTableColumns;
use Exception;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;

class OverviewPrescriptionsTable extends TableComponent
{
    public int $patientId;


    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(Prescription::query()->where('patient_id', $this->patientId))
            ->defaultSort('created_at', 'desc')
            ->heading('الوصفات طبية')
            ->emptyStateHeading('لا توجد وصفات طبية')
            ->emptyStateIcon('fas-prescription-bottle-medical')
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('المريض')
                    ->numeric()
                    ->alignCenter()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('الطبيب')
                    ->numeric()
                    ->alignCenter()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment.id')
                    ->label('الموعد')
                    ->alignCenter()
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('تاريخ الوصفة')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                ...SharedTableColumns::blame(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('إضافة وصفة')
                    ->url(fn() => route('filament.admin.resources.prescriptions.create')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(Prescription $record) => route('filament.admin.resources.prescriptions.view', $record)),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('edit')
                        ->label('تعديل')
                        ->icon('heroicon-o-pencil')
                        ->url(fn(Prescription $record) => route('filament.admin.resources.prescriptions.edit', $record)),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

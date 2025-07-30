<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStockMovement extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'آخر حركات المخزون';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(
                StockMovement::with(['fromCenter', 'toCenter', 'patient', 'supplier'])
                    ->latest('created_at')
                    ->take(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'in' => 'فاتورة مشتريات',
                        'out' => 'طلب مريض',
                        'transfer' => 'تحويل مخزون',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'in' => 'success',
                        'out' => 'warning',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->label('نوع الطلب'),
                Tables\Columns\TextColumn::make('actor.name')
                    ->label('بواسطة')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('رقم الطلب')
                    ->badge()
                    ->alignCenter()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('fromCenter.name')
                    ->label('من المركز')
                    ->numeric()
                    ->alignCenter()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('toCenter.name')
                    ->label('إلي المركز')
                    ->placeholder('-')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('إلى المريض')
                    ->numeric()
                    ->placeholder('-')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('من المورد')
                    ->placeholder('-')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('بتاريخ')
                    ->alignCenter()
                    ->date('d/m/Y'),
            ])
            ->emptyStateHeading('لا توجد حركات مخزون حديثة')
            ->defaultSort('created_at', 'desc');
    }
}

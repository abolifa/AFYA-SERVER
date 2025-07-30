<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockProduct extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'المنتجات منخفضة المخزون';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->query())
            ->columns([
                TextColumn::make('name')
                    ->label('المنتج')
                    ->limit(28),

                TextColumn::make('type')
                    ->label('النوع')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'medicine' => 'دواء',
                        'equipment' => 'معدات',
                        'service' => 'خدمة',
                        'other' => 'أخرى',
                        default => $state
                    })
                    ->color(fn($state) => match ($state) {
                        'medicine' => 'info',
                        'equipment' => 'warning',
                        'service' => 'success',
                        'other' => 'danger',
                    })
                    ->badge(),


                TextColumn::make('stock')
                    ->label('المخزون')
                    ->alignCenter()
                    ->color(fn($state, $record) => $state <= 0
                        ? 'danger'
                        : ($state <= $record->alert_threshold ? 'warning' : 'success')),


            ])
            ->defaultSort('stock')
            ->paginated(false);
    }

    protected function query(): Builder
    {
        $q = Product::query()
            ->select('products.*')
            ->selectRaw("
                (
                    -- IN: invoices confirmed
                    COALESCE((
                        SELECT SUM(ii.quantity)
                        FROM invoice_items ii
                        JOIN invoices iv ON iv.id = ii.invoice_id
                        WHERE ii.product_id = products.id
                          AND iv.status = 'confirmed'
                    ),0)
                    +
                    -- IN: transfer IN (to any center)
                    COALESCE((
                        SELECT SUM(tii.quantity)
                        FROM transfer_invoice_items tii
                        JOIN transfer_invoices ti ON ti.id = tii.transfer_invoice_id
                        WHERE tii.product_id = products.id
                          AND ti.status='confirmed'
                    ),0)
                    -
                    -- OUT: patient orders
                    COALESCE((
                        SELECT SUM(oi.quantity)
                        FROM order_items oi
                        JOIN orders o ON o.id = oi.order_id
                        WHERE oi.product_id = products.id
                          AND o.status='confirmed'
                    ),0)
                    -
                    -- OUT: transfer OUT (from any center)
                    COALESCE((
                        SELECT SUM(tii2.quantity)
                        FROM transfer_invoice_items tii2
                        JOIN transfer_invoices ti2 ON ti2.id = tii2.transfer_invoice_id
                        WHERE tii2.product_id = products.id
                          AND ti2.status='confirmed'
                    ),0)
                ) AS stock
            ")
            ->havingRaw('stock <= alert_threshold')
            ->orderBy('stock', 'asc')
            ->limit(5);

        return $q;
    }
}

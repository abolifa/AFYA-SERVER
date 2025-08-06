<?php

namespace App\Filament\Pages;

use App\Models\Product;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Exception;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ProductStock extends Page implements HasTable, Forms\Contracts\HasForms
{
    use InteractsWithTable, HasPageShield;
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'fas-warehouse';
    protected static string $view = 'filament.pages.product-stock';
    protected static ?string $title = 'المخزن';
    protected static ?string $navigationGroup = "إدارة المخزون";
    public ?int $selectedCenter = null;
    public bool $showZeroStock = false;

    public function getHeading(): string|Htmlable
    {
        if (auth()->user()->hasRole(['admin', 'super_admin'])) {
            return 'جميع المخازن';
        } else {
            return ' مخزن ' . auth()->user()->center?->name ?? 'بدون مركز';
        }
    }

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->hasRole(['admin', 'super_admin'])) {
            $this->selectedCenter = null;
        } elseif ($user->center_id == null) {
            $this->selectedCenter = "no_center";
        } else {
            $this->selectedCenter = $user->center_id;
        }

        $this->form->fill([
            'selectedCenter' => $this->selectedCenter,
            'showZeroStock' => $this->showZeroStock,
        ]);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $base = Product::query()->select('products.*');
                if (is_null($this->selectedCenter)) {
                    $base = $base->selectRaw(/** @lang sql */ '
                        (
                            SELECT COALESCE(SUM(ii.quantity), 0)
                            FROM invoice_items AS ii
                            JOIN invoices AS iv ON iv.id = ii.invoice_id
                            WHERE ii.product_id = products.id
                              AND iv.status = "confirmed"
                              AND iv.deleted_at IS NULL
                        )
                        -
                        (
                            SELECT COALESCE(SUM(oi.quantity), 0)
                            FROM order_items AS oi
                            JOIN orders AS o ON o.id = oi.order_id
                            WHERE oi.product_id = products.id
                              AND o.status = "confirmed"
                              AND o.deleted_at IS NULL
                        )
                        AS stock
                    ');
                } else {
                    $base = $base->selectRaw(/** @lang sql */ '
                        (
                          SELECT COALESCE(SUM(ii.quantity),0)
                          FROM invoice_items AS ii
                          JOIN invoices AS iv ON iv.id = ii.invoice_id
                          WHERE ii.product_id = products.id
                            AND iv.center_id  = ?
                            AND iv.status     = "confirmed"
                            AND iv.deleted_at IS NULL
                        )
                        +
                        (
                          SELECT COALESCE(SUM(tii.quantity),0)
                          FROM transfer_invoice_items AS tii
                          JOIN transfer_invoices AS ti ON ti.id = tii.transfer_invoice_id
                          WHERE tii.product_id   = products.id
                            AND ti.to_center_id  = ?
                            AND ti.status        = "confirmed"
                            AND ti.deleted_at IS NULL
                        )
                        -
                        (
                          SELECT COALESCE(SUM(oi.quantity),0)
                          FROM order_items AS oi
                          JOIN orders AS o ON o.id = oi.order_id
                          WHERE oi.product_id = products.id
                            AND o.center_id    = ?
                            AND o.status       = "confirmed"
                            AND o.deleted_at IS NULL
                        )
                        -
                        (
                          SELECT COALESCE(SUM(tii.quantity),0)
                          FROM transfer_invoice_items AS tii
                          JOIN transfer_invoices AS ti ON ti.id = tii.transfer_invoice_id
                          WHERE tii.product_id     = products.id
                            AND ti.from_center_id  = ?
                            AND ti.status          = "confirmed"
                            AND ti.deleted_at IS NULL
                        )
                        AS stock
                    ', [
                        $this->selectedCenter,
                        $this->selectedCenter,
                        $this->selectedCenter,
                        $this->selectedCenter,
                    ]);
                }

                if ($this->showZeroStock) {
                    $base->havingRaw('stock != 0');
                }

                return $base;
            })
            ->columns([
                TextColumn::make('name')
                    ->label('المنتج')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'medicine' => 'دواء',
                        'equipment' => 'معدات طبية',
                        'service' => 'خدمة طبية',
                        'other' => 'أخرى',
                    })
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('المخزون')
                    ->numeric()
                    ->sortable()
                    ->color(fn(int $state, Product $record) => match (true) {
                        $state === 0 => 'danger',
                        $state <= $record->alert_threshold => 'warning',
                        default => 'success',
                    })
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('product')
                    ->label('المنتج')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->attribute('products.id'),
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'medicine' => 'دواء',
                        'equipment' => 'معدات طبية',
                        'service' => 'خدمة طبية',
                        'other' => 'أخرى',
                    ])
                    ->native(false)
                    ->attribute('products.type'),
            ], layout: FiltersLayout::Dropdown);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make([
//                Select::make('selectedCenter')
//                    ->label('اختر المركز')
//                    ->options(Center::pluck('name', 'id'))
//                    ->searchable()
//                    ->reactive()
//                    ->placeholder('جميع المراكز')
//                    ->afterStateUpdated(fn() => $this->resetTable()),

                Forms\Components\ToggleButtons::make('showZeroStock')
                    ->label('إستثناء المخزون صفر')
                    ->boolean()
                    ->inline()
                    ->grouped()
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->resetTable()),
            ])->columns(),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Center;
use App\Models\InvoiceItem;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\TransferInvoiceItem;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class ProductMovementHistory extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'إدارة المخزون';
    protected static ?string $title = 'حركة المنتج';
    protected static string $view = 'filament.pages.product-movement-history'; // new blade

    public ?int $product_id = null;
    public ?int $center_id = null;

    protected ?Collection $cache = null;


    public function getMovements(): Collection
    {
        if ($this->cache !== null) return $this->cache;
        if (!$this->product_id) return $this->cache = collect();

        $pid = $this->product_id;
        $cid = $this->center_id;

        $invoices = InvoiceItem::with(['invoice.center', 'invoice.supplier'])
            ->where('product_id', $pid)
            ->whereHas('invoice', fn($q) => $q->where('status', 'confirmed')
                ->when($cid, fn($qq) => $qq->where('center_id', $cid)))
            ->get()
            ->map(function ($ii) {
                $iv = $ii->invoice;
                return [
                    'date' => $iv->created_at,
                    'direction' => 'IN',
                    'qty' => (int)$ii->quantity,
                    'from_center' => optional($iv->center)->name,
                    'to_center' => optional($iv->center)->name,
                    'supplier' => optional($iv->supplier)->name,
                    'patient' => null,
                ];
            });

        $orders = OrderItem::with(['order.center', 'order.patient'])
            ->where('product_id', $pid)
            ->whereHas('order', fn($q) => $q->where('status', 'confirmed')
                ->when($cid, fn($qq) => $qq->where('center_id', $cid)))
            ->get()
            ->map(function ($oi) {
                $o = $oi->order;
                return [
                    'date' => $o->created_at,
                    'direction' => 'OUT',
                    'qty' => -1 * (int)$oi->quantity,
                    'from_center' => optional($o->center)->name,
                    'to_center' => optional($o->center)->name,
                    'supplier' => null,
                    'patient' => optional($o->patient)->name,
                ];
            });

        $transfersRaw = TransferInvoiceItem::with(['transferInvoice.fromCenter', 'transferInvoice.toCenter'])
            ->where('product_id', $pid)
            ->whereHas('transferInvoice', fn($q) => $q->where('status', 'confirmed')
                ->when($cid, fn($qq) => $qq->where(function ($x) use ($cid) {
                    $x->where('from_center_id', $cid)->orWhere('to_center_id', $cid);
                })))
            ->get()
            ->flatMap(function ($tii) use ($cid) {
                $ti = $tii->transferInvoice;
                $rows = collect();
                $outOK = !$cid || $ti->from_center_id == $cid;
                $inOK = !$cid || $ti->to_center_id == $cid;
                if ($outOK) {
                    $rows->push([
                        'date' => $ti->created_at,
                        'direction' => 'TR_OUT',
                        'qty' => -1 * (int)$tii->quantity,
                        'from_center' => optional($ti->fromCenter)->name,
                        'to_center' => optional($ti->toCenter)->name,
                        'supplier' => null,
                        'patient' => null,
                    ]);
                }
                if ($inOK) {
                    $rows->push([
                        'date' => $ti->created_at,
                        'direction' => 'TR_IN',
                        'qty' => (int)$tii->quantity,
                        'from_center' => optional($ti->fromCenter)->name,
                        'to_center' => optional($ti->toCenter)->name,
                        'supplier' => null,
                        'patient' => null,
                    ]);
                }
                return $rows;
            });

        $all = $invoices->concat($orders)->concat($transfersRaw)
            ->sortBy([['date', 'asc']])
            ->values();

        $balance = 0;
        $all = $all->map(function ($row) use (&$balance) {
            $balance += $row['qty'];
            $row['running'] = $balance;
            return $row;
        });

        return $this->cache = $all;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('اختيار')
                ->schema([
                    Select::make('product_id')
                        ->label('المنتج')
                        ->options(Product::orderBy('name')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn() => $this->cache = null),

                    Select::make('center_id')
                        ->label('المركز (اختياري)')
                        ->options(Center::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('جميع المراكز')
                        ->reactive()
                        ->afterStateUpdated(fn() => $this->cache = null),
                ])
                ->columns(2),
        ];
    }
}

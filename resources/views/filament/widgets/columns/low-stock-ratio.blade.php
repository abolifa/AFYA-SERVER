@php
    /** @var Product $record */
    use App\Models\Product;
    $stock = (int) ($record->stock ?? 0);
    $threshold = (int) ($record->alert_threshold ?? 0);
    $pct = $threshold > 0
        ? max(0, min(100, (int) round(($stock / max(1,$threshold)) * 100)))
        : 0;
@endphp
<div style="min-width:90px;">
    <div style="height:6px; background:#e5e7eb; border-radius:4px; position:relative; overflow:hidden;">
        <div style="
            height:100%;
            width:{{ $pct }}%;
            background:{{ $stock <= 0 ? '#dc2626'
                : ($stock <= $threshold ? '#f59e0b'
                : '#16a34a') }};
            transition:width .3s;"></div>
    </div>
    <div style="font-size:11px; margin-top:4px; text-align:center;">
        {{ $stock }} / {{ $threshold }}
    </div>
</div>

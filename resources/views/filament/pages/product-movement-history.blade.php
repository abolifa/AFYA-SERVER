<x-filament::page>
    {{ $this->form }}

    @if (! $product_id)
        <div style="margin-top:1rem; text-align:center; color:#6b7280;">
            اختر منتجاً لعرض الحركة.
        </div>
    @else
        @php
            $movements = $this->getMovements();
        @endphp

        @if ($movements->isEmpty())
            <div style="margin-top:1.5rem; text-align:center; color:#6b7280;">
                لا توجد حركات.
            </div>
        @else
            <div style="margin-top:1rem; overflow-x:auto; border:1px solid; border-radius:8px; background: transparent;" class="pmh-table-wrapper">

                <style>
                    .pmh-table-wrapper {
                        border-radius: 8px;
                        background: transparent;
                    }
                    .pmh-table-wrapper table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 14px;
                        background: transparent;
                    }
                    .pmh-table-wrapper thead th {
                        font-weight: 600;
                        padding: 15px;
                        white-space: nowrap;
                        text-align: center;
                        border-bottom: 1px solid rgba(120,130,140,0.35);
                        background: transparent;
                        color: inherit;
                    }
                    .pmh-table-wrapper tbody td {
                        vertical-align: middle;
                        white-space: nowrap;
                        text-align: center;
                        padding: 15px;

                        border-bottom: 1px solid rgba(120,130,140,0.15);
                        background: transparent;
                        color: inherit;
                    }
                    .pmh-table-wrapper tbody tr:last-child td {
                        border-bottom: none;
                    }
                    /* Remove hover highlight; keep pointer clarity optional */
                    .pmh-table-wrapper tbody tr:hover td {
                        background: transparent;
                    }
                    /* “Badge” now simple text – remove pill styles */
                    .pmh-badge {
                        font-weight: 600;
                        font-size: 12px;
                        background: transparent !important;
                        color: inherit !important;
                        padding: 0;
                        border: none;
                    }
                    /* Quantity & balance neutral (no colors) */
                    .pmh-num-pos,
                    .pmh-num-neg,
                    .pmh-balance-pos,
                    .pmh-balance-neg {
                        color: inherit !important;
                        font-weight: 600;
                    }
                </style>

                <table>
                    <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>الكمية (±)</th>
                        <th>الرصيد التراكمي</th>
                        <th>من المركز</th>
                        <th>إلى المركز</th>
                        <th>المورد</th>
                        <th>المريض</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($movements as $row)
                        @php
                            $dirCode = $row['direction'];
                            $dirLabel = match($dirCode) {
                                'IN' => 'وارد (فاتورة)',
                                'OUT' => 'صادر (طلب مريض)',
                                'TR_IN' => 'تحويل وارد',
                                'TR_OUT' => 'تحويل صادر',
                                default => $dirCode
                            };
                            $badgeClass = match($dirCode) {
                                'IN' => 'in',
                                'OUT' => 'out',
                                'TR_IN' => 'trin',
                                'TR_OUT' => 'trout',
                                default => ''
                            };
                            $qtyClass = $row['qty'] < 0 ? 'pmh-num-neg' : 'pmh-num-pos';
                            $balClass = $row['running'] < 0 ? 'pmh-balance-neg' : 'pmh-balance-pos';
                        @endphp
                        <tr>
                            <td>{{ $row['date']->format('Y-m-d H:i') }}</td>
                            <td><span class="pmh-badge {{ $badgeClass }}">{{ $dirLabel }}</span></td>
                            <td class="{{ $qtyClass }}">
                                {{ $row['qty'] > 0 ? '+' : '' }}{{ $row['qty'] }}
                            </td>
                            <td class="{{ $balClass }}">{{ $row['running'] }}</td>
                            <td>{{ $row['from_center'] ?? '-' }}</td>
                            <td>{{ $row['to_center'] ?? '-' }}</td>
                            <td>{{ $row['supplier'] ?? '-' }}</td>
                            <td>{{ $row['patient'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</x-filament::page>

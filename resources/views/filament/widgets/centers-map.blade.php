<x-filament::widget>
    <x-filament::card>
        <div id="centers-map" style="width:100%;height:450px;border-radius:10px;"></div>
        <input type="hidden" id="centers-data" value='@json($centers, JSON_UNESCAPED_UNICODE)'/>
    </x-filament::card>
</x-filament::widget>

@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
        <style>
            .leaflet-popup-content-wrapper {
                font-size: 13px;
                direction: rtl;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            document.addEventListener('livewire:navigated', initCentersMap);
            document.addEventListener('DOMContentLoaded', initCentersMap);

            function initCentersMap() {
                const el = document.getElementById('centers-map');
                if (!el || el.dataset.inited) return;
                el.dataset.inited = '1';

                const raw = document.getElementById('centers-data')?.value ?? '[]';
                let centers = [];
                try {
                    centers = JSON.parse(raw);
                } catch (e) {
                }

                const map = L.map('centers-map').setView([27, 17], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                const bounds = [];
                centers.forEach(c => {
                    if (typeof c.lat !== 'number' || typeof c.lng !== 'number') return;
                    const html = `<div style="min-width:160px"><strong>${c.name ?? 'مركز'}</strong><br>`
                        + (c.address ? `<span style="color:#555">${c.address}</span><br>` : '')
                        + (c.phone ? `<span style="font-size:12px;">📞 ${c.phone}</span>` : '')
                        + `</div>`;
                    L.marker([c.lat, c.lng]).bindPopup(html).addTo(map);
                    bounds.push([c.lat, c.lng]);
                });
                if (bounds.length > 1) map.fitBounds(bounds, {padding: [30, 30]});
                else if (bounds.length === 1) map.setView(bounds[0], 12);
            }
        </script>
    @endpush
@endonce

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />
@endpush

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- Map Card Header -->
    <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-red-600 mb-1">
                <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                Peta Sebaran Kejadian Bencana
            </div>
            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">
                Peta Interaktif Laporan Insiden Warga Desa Kalikurmo
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Menampilkan seluruh titik koordinat laporan kejadian warga di Desa Kalikurmo. Klik pada peta untuk menjatuhkan pin dan mengisi koordinat laporan secara otomatis.
            </p>
        </div>

        <div class="flex items-center gap-2 text-xs font-semibold shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                Total Titik Insiden: {{ $reports->count() }}
            </span>
        </div>
    </div>

    <!-- Leaflet Map Container -->
    <div class="relative isolate">
        <div id="homepage-incident-map" class="w-full h-[460px] sm:h-[520px] relative z-0 bg-slate-100"></div>
    </div>
</div>

@php
    $incidentMarkers = $reports->map(fn (\App\Models\Report $report): array => [
        'id' => $report->id,
        'reporter_name' => $report->reporter_name,
        'category' => $report->category,
        'description' => $report->description,
        'latitude' => (float) $report->latitude,
        'longitude' => (float) $report->longitude,
        'status' => $report->status,
        'image_url' => $report->image_url,
        'created_at' => $report->created_at?->diffForHumans() ?? 'Baru saja',
    ])->values();
@endphp

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapElement = document.getElementById('homepage-incident-map');
            if (!mapElement) {
                return;
            }

            // Hardcoded initial view centered on Kalikurmo (-7.192342, 110.563481, zoom 15)
            const map = L.map('homepage-incident-map', {
                scrollWheelZoom: true
            }).setView([-7.192342, 110.563481], 15);

            // Standard OpenStreetMap Basemap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Desa Kalikurmo'
            }).addTo(map);

            // Loop through incident reports from database and render dynamic markers
            const incidents = @json($incidentMarkers);

            incidents.forEach(function (report) {
                const lat = parseFloat(report.latitude);
                const lng = parseFloat(report.longitude);

                if (Number.isNaN(lat) || Number.isNaN(lng)) {
                    return;
                }

                const marker = L.marker([lat, lng]).addTo(map);

                const popupHtml = `
                    <div class="p-1 max-w-[240px]">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-red-50 text-red-700">
                                ${report.category}
                            </span>
                            <span class="text-[10px] text-slate-400">${report.created_at}</span>
                        </div>
                        <div class="font-bold text-xs text-slate-900 mb-1">Pelapor: ${report.reporter_name}</div>
                        <p class="text-xs text-slate-600 mb-2">${report.description}</p>
                        ${report.image_url ? `<img src="${report.image_url}" alt="Foto Laporan" class="w-full h-28 object-cover rounded-lg border border-slate-200 mb-1.5" />` : ''}
                        <div class="text-[10px] font-mono text-slate-500">
                            Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupHtml);
            });

            // Click on map to drop a pin and auto-fill the form input fields
            let pickedMarker = null;
            map.on('click', function (event) {
                const lat = event.latlng.lat.toFixed(6);
                const lng = event.latlng.lng.toFixed(6);

                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                const latDisplay = document.getElementById('latitude_display');
                const lngDisplay = document.getElementById('longitude_display');

                if (latInput && lngInput) {
                    latInput.value = lat;
                    lngInput.value = lng;
                }

                if (latDisplay && lngDisplay) {
                    latDisplay.value = lat;
                    lngDisplay.value = lng;
                }

                if (pickedMarker) {
                    pickedMarker.setLatLng(event.latlng);
                } else {
                    pickedMarker = L.marker(event.latlng).addTo(map);
                }

                pickedMarker.bindPopup(
                    `<div class="text-xs font-semibold text-slate-800">Pin Lokasi Laporan:<br><code class="font-mono text-red-600">${lat}, ${lng}</code></div>`
                ).openPopup();
            });
        });
    </script>
@endpush

@extends('layouts.app')

@section('title', 'Lapor Kejadian Bencana — SI-TANGGUH Desa Kalikurmo')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />
@endpush

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900">
                &larr; Kembali ke Beranda &amp; Peta Utama
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                Formulir Pelaporan Kejadian Bencana Warga
            </h1>
            <p class="text-sm text-slate-600 mt-1">
                Kirimkan laporan kejadian bencana di wilayah Desa Kalikurmo, Kabupaten Semarang. Klik titik lokasi pada peta untuk menjatuhkan pin dan mengisi koordinat secara otomatis.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Form Column -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Hidden Input Fields Populated by Map Click -->
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', '-7.192342') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', '110.563481') }}">

                    <div>
                        <label for="reporter_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Lengkap Pelapor <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text"
                               id="reporter_name"
                               name="reporter_name"
                               value="{{ old('reporter_name') }}"
                               placeholder="Contoh: Siti Aminah (Dusun Krajan RT 01)"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20">
                    </div>

                    <div>
                        <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kategori Kejadian Bencana <span class="text-red-600">*</span>
                        </label>
                        <select id="category"
                                name="category"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20">
                            <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Pilih Kategori Kejadian --</option>
                            @foreach (['Tanah Longsor', 'Banjir / Luapan Sungai', 'Pohon Tumbang', 'Retakan Tanah / Tanggul Kritis', 'Infrastruktur Jalan/Jembatan Rusak', 'Kebakaran Pemukiman/Lahan', 'Lainnya'] as $cat)
                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Titik Koordinat Kejadian (Pin Peta) <span class="text-red-600">*</span>
                        </span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="latitude_display" class="block text-xs font-semibold text-slate-600 mb-1">
                                    Latitude
                                </label>
                                <input type="text"
                                       id="latitude_display"
                                       value="{{ old('latitude', '-7.192342') }}"
                                       readonly
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-mono text-slate-700">
                            </div>

                            <div>
                                <label for="longitude_display" class="block text-xs font-semibold text-slate-600 mb-1">
                                    Longitude
                                </label>
                                <input type="text"
                                       id="longitude_display"
                                       value="{{ old('longitude', '110.563481') }}"
                                       readonly
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-mono text-slate-700">
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">
                            Klik langsung pada peta Desa Kalikurmo di sebelah kanan untuk menjatuhkan pin dan mengisi koordinat secara otomatis.
                        </p>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kronologi &amp; Deskripsi Kejadian <span class="text-red-600">*</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  required
                                  placeholder="Tuliskan detail kondisi di lokasi, perkiraan dampak, apakah ada akses jalan yang tertutup, atau warga yang membutuhkan evakuasi..."
                                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Unggah Foto Kejadian <span class="text-red-600">*</span>
                        </label>
                        <input type="file"
                               id="image"
                               name="image"
                               accept="image/jpeg,image/png,image/webp"
                               required
                               class="block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 border border-slate-300 rounded-xl p-1.5">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm py-3.5 px-5 shadow-sm transition-colors cursor-pointer">
                            Kirim Laporan &amp; Kompres Foto
                        </button>
                    </div>
                </form>
            </div>

            <!-- Interactive Coordinate Picker Map -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden isolate relative z-10 lg:sticky lg:top-24">
                <div class="p-4 border-b border-slate-200">
                    <h2 class="font-bold text-slate-900 text-sm">Peta Penentu Titik Koordinat (Desa Kalikurmo)</h2>
                    <p class="text-xs text-slate-500">Klik lokasi kejadian pada peta di bawah ini untuk menjatuhkan pin:</p>
                </div>
                <div id="picker-map" class="w-full h-96 bg-slate-100 relative z-0"></div>
                <div class="p-4 bg-slate-50 text-xs text-slate-600 space-y-1">
                    <div><strong>Petunjuk Pemilihan Titik Lokasi:</strong></div>
                    <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                        <li>Peta dikunci awal tepat di wilayah Desa Kalikurmo (<code>-7.192342, 110.563481</code>).</li>
                        <li>Klik titik kejadian pada peta untuk memindahkan pin dan memperbarui nilai Latitude &amp; Longitude.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const latDisplay = document.getElementById('latitude_display');
            const lngDisplay = document.getElementById('longitude_display');

            // Hardcoded initial map center (-7.192342, 110.563481) with zoom level 15
            const initialCenter = [-7.192342, 110.563481];

            const pickerMap = L.map('picker-map').setView(initialCenter, 15);

            // Standard OpenStreetMap Basemap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(pickerMap);

            let marker = L.marker(initialCenter).addTo(pickerMap);

            pickerMap.on('click', function (e) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);

                marker.setLatLng(e.latlng);
                latInput.value = lat;
                lngInput.value = lng;

                if (latDisplay && lngDisplay) {
                    latDisplay.value = lat;
                    lngDisplay.value = lng;
                }

                marker.bindPopup(
                    `<div class="text-xs font-semibold">Pin Lokasi Kejadian:<br><code class="font-mono text-red-600">${lat}, ${lng}</code></div>`
                ).openPopup();
            });
        });
    </script>
@endpush

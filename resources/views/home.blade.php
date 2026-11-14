@extends('layouts.app')

@section('title', 'SI-TANGGUH Desa Kalikurmo — Peta Sebaran Kejadian, Pelaporan Warga & Logistik Evakuasi')

@section('content')
    <!-- Hero & Status Summary Section -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 mb-3">
                        Pusat Pengendalian &amp; Mitigasi Bencana Desa Kalikurmo
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Pantau Status Darurat, Peta Titik Kejadian &amp; Laporan Warga
                    </h1>
                    <p class="mt-3 text-sm sm:text-base text-slate-600 leading-relaxed">
                        Platform terpadu kesiapsiagaan bencana Desa Kalikurmo, Kabupaten Semarang. Dilengkapi peta interaktif titik laporan insiden warga secara waktu-nyata, kompresi foto otomatis, serta transparansi stok logistik posko pengungsian.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="#form-lapor-cepat"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-sm transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Laporkan Kejadian Sekarang
                    </a>
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-sm transition-all">
                        Kelola Status &amp; Logistik
                    </a>
                </div>
            </div>

            <!-- Key Metrics Strip -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
                @php
                    $statusCardStyle = match ($alertStatus->level) {
                        \App\Models\AlertStatus::LEVEL_EMERGENCY => 'border-red-300 bg-red-50/70 text-red-900',
                        \App\Models\AlertStatus::LEVEL_ALERT => 'border-amber-300 bg-amber-50/70 text-amber-900',
                        default => 'border-emerald-300 bg-emerald-50/70 text-emerald-900',
                    };
                @endphp
                <div class="rounded-2xl border p-4 sm:p-5 {{ $statusCardStyle }}">
                    <div class="text-xs font-bold uppercase tracking-wider opacity-75">Level Kesiapsiagaan Desa</div>
                    <div class="text-xl sm:text-2xl font-extrabold mt-1">{{ $alertStatus->label() }}</div>
                    <div class="text-xs mt-1.5 opacity-80">Level Sistem: <span class="font-mono font-bold">{{ $alertStatus->level }}</span></div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Laporan Warga Masuk</div>
                    <div class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">{{ $reports->count() }} Laporan</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Inventaris Logistik Posko</div>
                    <div class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">{{ $logistics->count() }} Jenis Bantuan</div>
                    <div class="text-xs text-slate-500 mt-1.5">Total Stok: {{ number_format($logistics->sum('quantity')) }} unit tersedia</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Grid: Interactive Leaflet Map + Citizen Reporting Form -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left 7 Columns: Interactive Leaflet Map with Dynamic Incident Markers -->
            <div class="lg:col-span-7 space-y-6">
                @include('partials.map')
            </div>

            <!-- Right 5 Columns: Citizen Incident Reporting Form -->
            <div id="form-lapor-cepat" class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-red-600">Pelaporan Partisipatif</span>
                        <h2 class="text-lg font-extrabold text-slate-900">Form Laporan Kejadian Warga</h2>
                    </div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        Auto-Compress Photo
                    </span>
                </div>

                <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Hidden Coordinate Inputs Auto-Populated by Map Pin Drop -->
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', '-7.192342') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', '110.563481') }}">

                    <div>
                        <label for="reporter_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Pelapor <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text"
                               id="reporter_name"
                               name="reporter_name"
                               value="{{ old('reporter_name') }}"
                               placeholder="Contoh: Budi Santoso (RT 02 / RW 01)"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20">
                    </div>

                    <div>
                        <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kategori Bencana / Kejadian <span class="text-red-600">*</span>
                        </label>
                        <select id="category"
                                name="category"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20">
                            <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Pilih Kategori Kejadian --</option>
                            @foreach (['Tanah Longsor', 'Banjir / Luapan Sungai', 'Pohon Tumbang', 'Retakan Tanah / Tanggul Kritis', 'Infrastruktur Jalan/Jembatan Rusak', 'Kebakaran Pemukiman/Lahan', 'Lainnya'] as $cat)
                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Koordinat Lokasi Pin Peta <span class="text-red-600">*</span>
                        </span>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="latitude_display" class="block text-[11px] font-semibold text-slate-500 mb-1">Latitude</label>
                                <input type="text"
                                       id="latitude_display"
                                       value="{{ old('latitude', '-7.192342') }}"
                                       readonly
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm font-mono text-slate-700">
                            </div>
                            <div>
                                <label for="longitude_display" class="block text-[11px] font-semibold text-slate-500 mb-1">Longitude</label>
                                <input type="text"
                                       id="longitude_display"
                                       value="{{ old('longitude', '110.563481') }}"
                                       readonly
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm font-mono text-slate-700">
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Klik pada peta Desa Kalikurmo di samping untuk menjatuhkan pin dan mengisi koordinat secara otomatis.
                        </p>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Deskripsi Kondisi Lapangan <span class="text-red-600">*</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  required
                                  placeholder="Jelaskan kronologi singkat, dampak kerusakan, atau kebutuhan mendesak di lokasi..."
                                  class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Foto Bukti Kejadian <span class="text-red-600">*</span>
                        </label>
                        <input type="file"
                               id="image"
                               name="image"
                               accept="image/jpeg,image/png,image/webp"
                               required
                               class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 border border-slate-300 rounded-xl p-1.5">
                        <p class="text-[11px] text-slate-500 mt-1">
                            Format JPG, PNG, atau WEBP (maks. 10MB)
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm py-3 px-4 shadow-sm transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        Kirim Laporan Kejadian
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Evacuation Center Logistics Inventory Section -->
    <section id="logistik" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Kesiapsiagaan Posko Pengungsian</span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-0.5">
                    Ketersediaan Logistik &amp; Peralatan Evakuasi Desa
                </h2>
                <p class="text-sm text-slate-500">
                    Data inventaris waktu-nyata yang dikelola oleh petugas Posko Utama Balai Desa Kalikurmo.
                </p>
            </div>
            <a href="{{ route('admin.dashboard') }}#crud-logistik"
               class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900">
                Update Stok di Panel Admin &rarr;
            </a>
        </div>

        @if ($logistics->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500 text-sm">
                Belum ada data inventaris logistik yang tercatat. Silakan tambahkan melalui <a href="{{ route('admin.dashboard') }}" class="text-red-600 font-semibold underline">Dashboard Admin</a>.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($logistics as $item)
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                    {{ $item->location ?? 'Posko Utama' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $item->quantity > 10 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $item->quantity > 10 ? 'Tersedia' : 'Stok Terbatas' }}
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base">{{ $item->item_name }}</h3>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-baseline justify-between">
                            <span class="text-xs text-slate-500">Jumlah Tersedia:</span>
                            <span class="text-xl font-extrabold text-slate-900">
                                {{ number_format($item->quantity) }}
                                <span class="text-xs font-semibold text-slate-500">{{ $item->unit }}</span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Citizen Incident Reports Section -->
    <section id="laporan-warga" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Pantauan Lapangan Warga</span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-0.5">
                    Daftar Laporan Kejadian Warga
                </h2>
                <p class="text-sm text-slate-500">
                    Dokumentasi insiden yang dikirimkan warga beserta titik koordinat dan foto yang telah dikompresi.
                </p>
            </div>
            <a href="{{ route('reports.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs sm:text-sm font-bold hover:bg-slate-800">
                + Kirim Laporan Baru
            </a>
        </div>

        @if ($reports->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
                <p class="text-slate-600 font-semibold">Belum ada laporan kejadian bencana yang masuk.</p>
                <p class="text-xs text-slate-400 mt-1">Gunakan formulir di atas apabila Anda menemukan potensi bahaya atau kejadian bencana di sekitar Desa Kalikurmo.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($reports as $report)
                    <article class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
                        <div class="relative h-48 bg-slate-100">
                            <img src="{{ $report->image_url }}"
                                 alt="{{ $report->category }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-900/85 text-white backdrop-blur">
                                    {{ $report->category }}
                                </span>
                            </div>
                            <div class="absolute top-3 right-3">
                                @php
                                    $statusBadge = match ($report->status) {
                                        'Resolved' => 'bg-emerald-600 text-white',
                                        'Verified' => 'bg-amber-500 text-white',
                                        default => 'bg-blue-600 text-white',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $statusBadge }}">
                                    {{ $report->status }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold text-slate-700">{{ $report->reporter_name }}</span>
                                    <span>{{ $report->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-700 leading-relaxed">
                                    {{ $report->description }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span class="font-mono text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                    📍 {{ number_format($report->latitude, 6) }}, {{ number_format($report->longitude, 6) }}
                                </span>
                                <span class="text-[11px] text-emerald-700 font-semibold">
                                    Terkompresi
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection

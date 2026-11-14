@extends('layouts.app')

@section('title', 'Dashboard Admin Posko — Kelola Status Darurat & Logistik Desa Kalikurmo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-10">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-red-600">
                    Pusat Komando Posko Desa Kalikurmo
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                    Dashboard Admin: Status Darurat &amp; Inventaris Logistik
                </h1>
                <p class="text-sm text-slate-600 mt-1">
                    Perubahan level status darurat di halaman ini akan langsung mengubah banner peringatan global di halaman utama warga.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <span class="hidden sm:inline-flex items-center gap-2 text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-2 rounded-xl">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Login: {{ auth()->user()->name ?? 'Admin' }} ({{ auth()->user()->username ?? 'admin' }})
                </span>
                <a href="#ganti-password"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <span>Ganti Password</span>
                </a>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700">
                    Lihat Halaman Publik &rarr;
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-xs sm:text-sm font-bold text-red-700 transition-colors cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <!-- SECTION 1: Manage Village AlertStatus (Safe, Alert, Emergency) -->
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        1. Pengaturan Status Darurat Desa (AlertStatus)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Pilih level kedaruratan desa saat ini: <strong>Safe</strong> (Aman), <strong>Alert</strong> (Waspada), atau <strong>Emergency</strong> (Darurat).
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider
                    {{ $alertStatus->level === 'Emergency' ? 'bg-red-100 text-red-800' : ($alertStatus->level === 'Alert' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                    Status Aktif: {{ $alertStatus->level }} ({{ $alertStatus->label() }})
                </span>
            </div>

            <form action="{{ route('admin.alert-status.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Radio Cards for Emergency Level -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- SAFE -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all {{ old('level', $alertStatus->level) === 'Safe' ? 'border-emerald-600 bg-emerald-50/50' : 'border-slate-200 hover:border-emerald-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-emerald-700">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                LEVEL: SAFE
                            </span>
                            <input type="radio"
                                   name="level"
                                   value="Safe"
                                   {{ old('level', $alertStatus->level) === 'Safe' ? 'checked' : '' }}
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <div class="font-bold text-slate-900 text-base">Aman (Normal)</div>
                        <p class="text-xs text-slate-600 mt-1">
                            Kondisi desa terkendali. Tidak ada ancaman bencana aktif, aktivitas warga berjalan normal.
                        </p>
                    </label>

                    <!-- ALERT -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all {{ old('level', $alertStatus->level) === 'Alert' ? 'border-amber-500 bg-amber-50/50' : 'border-slate-200 hover:border-amber-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-amber-700">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                LEVEL: ALERT
                            </span>
                            <input type="radio"
                                   name="level"
                                   value="Alert"
                                   {{ old('level', $alertStatus->level) === 'Alert' ? 'checked' : '' }}
                                   class="h-4 w-4 text-amber-600 focus:ring-amber-500">
                        </div>
                        <div class="font-bold text-slate-900 text-base">Waspada (Siaga II)</div>
                        <p class="text-xs text-slate-600 mt-1">
                            Curah hujan ekstrem atau peningkatan potensi longsor/banjir. Warga di zona rawan diminta bersiap.
                        </p>
                    </label>

                    <!-- EMERGENCY -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all {{ old('level', $alertStatus->level) === 'Emergency' ? 'border-red-600 bg-red-50/50' : 'border-slate-200 hover:border-red-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-red-700">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-ping"></span>
                                LEVEL: EMERGENCY
                            </span>
                            <input type="radio"
                                   name="level"
                                   value="Emergency"
                                   {{ old('level', $alertStatus->level) === 'Emergency' ? 'checked' : '' }}
                                   class="h-4 w-4 text-red-600 focus:ring-red-500">
                        </div>
                        <div class="font-bold text-slate-900 text-base">Darurat (Siaga I / Evakuasi)</div>
                        <p class="text-xs text-slate-600 mt-1">
                            Bencana sedang berlangsung! Instruksikan warga segera menuju titik kumpul dan posko pengungsian.
                        </p>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="headline" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Judul Banner Peringatan Global
                            </label>
                            <span id="headline-sync-badge" class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Otomatis sinkron
                            </span>
                        </div>
                        <input type="text"
                               id="headline"
                               name="headline"
                               value="{{ old('headline', $alertStatus->headline) }}"
                               placeholder="Contoh: Status Desa Kalikurmo: WASPADA Cuaca Ekstrem"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    <div>
                        <label for="updated_by" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Petugas / Otoritas Piket Posko
                        </label>
                        <input type="text"
                               id="updated_by"
                               name="updated_by"
                               value="{{ old('updated_by', $alertStatus->updated_by ?? 'Satgas BPBD Desa Kalikurmo') }}"
                               placeholder="Contoh: Tim Satgas BPBD Desa Kalikurmo"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Pesan Instruksi / Imbauan untuk Warga
                        </label>
                        <button type="button"
                                id="btn-reset-default"
                                class="text-[11px] font-bold text-slate-500 hover:text-slate-800 underline cursor-pointer">
                            Reset ke teks default kondisi ini
                        </button>
                    </div>
                    <textarea id="message"
                              name="message"
                              rows="2"
                              placeholder="Tuliskan instruksi evakuasi atau imbauan kesiapsiagaan yang akan tampil di banner atas..."
                              class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">{{ old('message', $alertStatus->message) }}</textarea>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Teks di atas otomatis terisi mengikuti radio kondisi yang Anda klik. Anda tetap bebas mengubah atau menyesuaikan kata-katanya kapan saja.
                    </p>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-6 py-3 shadow-sm transition-colors cursor-pointer">
                        Simpan &amp; Publikasikan Status Darurat
                    </button>
                </div>
            </form>
        </section>

        <!-- SECTION 2: CRUD Operations for Evacuation Center Logistics -->
        <section id="crud-logistik" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left 4 Columns: Create / Edit Logistic Item Form -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="border-b border-slate-100 pb-4 mb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">
                        {{ $editingLogistic ? 'Mode Edit Data' : 'Tambah Stok Baru' }}
                    </span>
                    <h2 class="text-lg font-extrabold text-slate-900 mt-0.5">
                        {{ $editingLogistic ? 'Perbarui Item Logistik' : '2. Input Logistik Posko' }}
                    </h2>
                </div>

                <form action="{{ $editingLogistic ? route('logistics.update', $editingLogistic) : route('logistics.store') }}"
                      method="POST"
                      class="space-y-4">
                    @csrf
                    @if ($editingLogistic)
                        @method('PUT')
                    @endif

                    <div>
                        <label for="item_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Barang / Logistik <span class="text-red-600">*</span>
                        </label>
                        <input type="text"
                               id="item_name"
                               name="item_name"
                               value="{{ old('item_name', $editingLogistic?->item_name) }}"
                               required
                               placeholder="Contoh: Beras Cadangan Posko"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="quantity" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Jumlah (Qty) <span class="text-red-600">*</span>
                            </label>
                            <input type="number"
                                   id="quantity"
                                   name="quantity"
                                   min="0"
                                   value="{{ old('quantity', $editingLogistic?->quantity ?? 0) }}"
                                   required
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                        </div>

                        <div>
                            <label for="unit" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Satuan (Unit) <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   id="unit"
                                   name="unit"
                                   value="{{ old('unit', $editingLogistic?->unit) }}"
                                   required
                                   placeholder="Kg, Kardus, Paket, Unit"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Lokasi Gudang / Posko Evakuasi
                        </label>
                        <input type="text"
                               id="location"
                               name="location"
                               value="{{ old('location', $editingLogistic?->location ?? 'Posko Utama Balai Desa') }}"
                               placeholder="Posko Utama Balai Desa"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-2.5 px-4 shadow-sm transition-colors cursor-pointer">
                            {{ $editingLogistic ? 'Simpan Perubahan' : '+ Tambah Logistik' }}
                        </button>

                        @if ($editingLogistic)
                            <a href="{{ route('admin.dashboard') }}#crud-logistik"
                               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm py-2.5 px-4">
                                Batal
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Right 8 Columns: Logistics Inventory Table -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Daftar Inventaris Logistik Posko Evakuasi</h3>
                        <p class="text-xs text-slate-500">Kelola stok kebutuhan darurat untuk warga terdampak bencana.</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                        Total: {{ $logistics->count() }} Item
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-5">Nama Item Logistik</th>
                                <th class="py-3.5 px-4">Lokasi Posko</th>
                                <th class="py-3.5 px-4">Kuantitas</th>
                                <th class="py-3.5 px-4">Satuan</th>
                                <th class="py-3.5 px-5 text-right">Aksi CRUD</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            @forelse ($logistics as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $editingLogistic?->id === $item->id ? 'bg-emerald-50/60' : '' }}">
                                    <td class="py-3.5 px-5 font-bold text-slate-900">
                                        {{ $item->item_name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-600">
                                        {{ $item->location ?? 'Posko Utama Balai Desa' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                        {{ number_format($item->quantity) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-xs font-semibold text-slate-600">
                                        {{ $item->unit }}
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <a href="{{ route('logistics.edit', $item) }}#crud-logistik"
                                               class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-xs font-bold text-slate-700">
                                                Edit
                                            </a>
                                            <form action="{{ route('logistics.destroy', $item) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus item logistik {{ addslashes($item->item_name) }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-xs font-bold text-red-700 cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 px-6 text-center text-slate-500 text-sm">
                                        Belum ada item logistik. Gunakan formulir di sebelah kiri untuk menambahkan stok logistik posko.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- SECTION 3: Citizen Incident Reports Management -->
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        3. Verifikasi &amp; Tindak Lanjut Laporan Warga (Reports)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Daftar laporan kejadian yang dikirim warga beserta foto bukti yang telah dikompresi.
                    </p>
                </div>
                <a href="{{ route('reports.create') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-800">
                    + Buat Laporan Baru
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-5">Foto</th>
                            <th class="py-3.5 px-4">Pelapor &amp; Waktu</th>
                            <th class="py-3.5 px-4">Kategori &amp; Koordinat</th>
                            <th class="py-3.5 px-4">Deskripsi</th>
                            <th class="py-3.5 px-4">Status Tindak Lanjut</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3.5 px-5">
                                    <a href="{{ $report->image_url }}" target="_blank" class="block w-16 h-12 rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                                        <img src="{{ $report->image_url }}" alt="Foto" class="w-full h-full object-cover">
                                    </a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $report->reporter_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $report->created_at?->format('d M Y H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-800">
                                        {{ $report->category }}
                                    </span>
                                    <div class="font-mono text-xs text-slate-500 mt-1">
                                        {{ number_format($report->latitude, 6) }}, {{ number_format($report->longitude, 6) }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs text-xs text-slate-600 leading-relaxed">
                                    {{ $report->description }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <form action="{{ route('reports.update-status', $report) }}" method="POST" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status"
                                                onchange="this.form.submit()"
                                                class="rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-bold text-slate-800">
                                            @foreach (['Pending', 'Verified', 'Resolved'] as $statusOption)
                                                <option value="{{ $statusOption }}" {{ $report->status === $statusOption ? 'selected' : '' }}>
                                                    {{ $statusOption }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <form action="{{ route('reports.destroy', $report) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus laporan kejadian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-xs font-bold text-red-700 cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-6 text-center text-slate-500 text-sm">
                                    Belum ada laporan kejadian dari warga.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reports->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $reports->links() }}
                </div>
            @endif
        </section>

        <!-- SECTION 4: Emergency Contacts Management -->
        <section id="kontak-darurat" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        4. Kelola Kontak Darurat Posko (Footer)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Nomor kontak di bawah ini akan langsung tampil secara publik di footer website bagi seluruh warga.
                    </p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                    {{ count($emergencyContacts) }} Nomor Terdaftar
                </span>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Form Tambah Kontak Baru -->
                <div class="lg:col-span-4 bg-slate-50 border border-slate-200 rounded-2xl p-5">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-4">+ Tambah Kontak Baru</h3>
                    <form action="{{ route('emergency-contacts.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nama Lembaga / Posko <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   required
                                   placeholder="Contoh: Babinsa / Bhabinkamtibmas"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:outline-none focus:border-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nomor Telepon / Call Center <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   name="number"
                                   required
                                   placeholder="Contoh: 0812-9988-7766"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-mono text-slate-900 focus:outline-none focus:border-slate-900">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Warna Teks
                                </label>
                                <select name="color" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none">
                                    <option value="text-white">Putih (Standar)</option>
                                    <option value="text-amber-400">Kuning / Amber</option>
                                    <option value="text-emerald-400">Hijau / Emerald</option>
                                    <option value="text-red-400">Merah</option>
                                    <option value="text-cyan-400">Biru Muda</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Urutan (Order)
                                </label>
                                <input type="number"
                                       name="order"
                                       value="{{ count($emergencyContacts) + 1 }}"
                                       min="0"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:outline-none">
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-4 shadow-sm transition-colors cursor-pointer">
                            + Simpan Kontak
                        </button>
                    </form>
                </div>

                <!-- Daftar & Edit Kontak yang Sudah Ada -->
                <div class="lg:col-span-8 space-y-3">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-2">Daftar Kontak Aktif (Dapat diedit langsung)</h3>
                    @forelse ($emergencyContacts as $contact)
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                            <form action="{{ route('emergency-contacts.update', $contact) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                @csrf
                                @method('PUT')
                                <div class="sm:col-span-4">
                                    <input type="text"
                                           name="name"
                                           value="{{ $contact->name }}"
                                           required
                                           placeholder="Nama Lembaga"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-bold text-slate-900 focus:outline-none focus:border-slate-900">
                                </div>
                                <div class="sm:col-span-4">
                                    <input type="text"
                                           name="number"
                                           value="{{ $contact->number }}"
                                           required
                                           placeholder="Nomor Telepon"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono text-slate-900 focus:outline-none focus:border-slate-900">
                                </div>
                                <div class="sm:col-span-2">
                                    <select name="color" class="w-full rounded-xl border border-slate-300 px-2 py-2 text-xs font-semibold text-slate-900 focus:outline-none">
                                        <option value="text-white" {{ $contact->color === 'text-white' ? 'selected' : '' }}>Putih</option>
                                        <option value="text-amber-400" {{ $contact->color === 'text-amber-400' ? 'selected' : '' }}>Kuning</option>
                                        <option value="text-emerald-400" {{ $contact->color === 'text-emerald-400' ? 'selected' : '' }}>Hijau</option>
                                        <option value="text-red-400" {{ $contact->color === 'text-red-400' ? 'selected' : '' }}>Merah</option>
                                        <option value="text-cyan-400" {{ $contact->color === 'text-cyan-400' ? 'selected' : '' }}>Biru</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2 flex items-center gap-1.5 justify-end">
                                    <button type="submit"
                                            title="Simpan Perubahan"
                                            class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors cursor-pointer">
                                        Simpan
                                    </button>
                            </form>
                                    <form action="{{ route('emergency-contacts.destroy', $contact) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus nomor {{ addslashes($contact->name) }}?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Kontak"
                                                class="p-2 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-bold transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500 py-6 text-center bg-slate-50 rounded-2xl border border-slate-200">
                            Belum ada kontak darurat. Tambahkan lewat formulir di samping.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- SECTION 5: Admin Account Security & Change Password -->
        <section id="ganti-password" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        5. Keamanan Akun &amp; Ganti Password Admin
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Perbarui password akun petugas posko (<strong>{{ auth()->user()->username ?? 'admin' }}</strong>) secara berkala untuk menjaga keamanan sistem.
                    </p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    Keamanan Akun
                </span>
            </div>

            <form action="{{ route('admin.password.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Password Saat Ini <span class="text-red-600">*</span>
                        </label>
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               required
                               placeholder="Masukkan password saat ini"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    <div>
                        <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Password Baru <span class="text-red-600">*</span>
                        </label>
                        <input type="password"
                               id="new_password"
                               name="password"
                               required
                               minlength="4"
                               placeholder="Minimal 4 karakter"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Konfirmasi Password Baru <span class="text-red-600">*</span>
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               minlength="4"
                               placeholder="Ulangi password baru"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-6 py-2.5 shadow-sm transition-colors cursor-pointer">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const defaults = {
                'Safe': {
                    headline: 'Status Desa Kalikurmo: Aman (Normal)',
                    message: 'Tidak ada ancaman bencana aktif saat ini. Warga diimbau tetap memantau informasi posko dan menjaga kelancaran drainase.'
                },
                'Alert': {
                    headline: 'Status Desa Kalikurmo: Waspada (Siaga Bencana)',
                    message: 'Curah hujan ekstrem atau peningkatan potensi longsor/banjir. Warga di zona rawan diminta bersiap.'
                },
                'Emergency': {
                    headline: 'PERINGATAN DARURAT DESA KALIKURMO: EVAKUASI SEGERA!',
                    message: 'Bencana sedang berlangsung! Seluruh warga di zona rawan harap segera menuju titik kumpul dan posko pengungsian.'
                }
            };

            const levelRadios = document.querySelectorAll('input[name="level"]');
            const headlineInput = document.getElementById('headline');
            const messageInput = document.getElementById('message');
            const resetBtn = document.getElementById('btn-reset-default');
            const syncBadge = document.getElementById('headline-sync-badge');

            let userHasCustomized = false;

            // When user types custom text manually
            if (headlineInput && messageInput) {
                const markCustom = function () {
                    userHasCustomized = true;
                    if (syncBadge) {
                        syncBadge.className = 'text-[11px] font-semibold text-amber-600 flex items-center gap-1';
                        syncBadge.innerHTML = 'Kustom (manual)';
                    }
                };
                headlineInput.addEventListener('input', markCustom);
                messageInput.addEventListener('input', markCustom);
            }

            // Function to apply default text for a given level
            function applyLevelDefaults(levelKey, force = false) {
                const data = defaults[levelKey];
                if (!data) return;

                if (force || !userHasCustomized) {
                    if (headlineInput) headlineInput.value = data.headline;
                    if (messageInput) messageInput.value = data.message;
                    if (syncBadge) {
                        syncBadge.className = 'text-[11px] font-semibold text-emerald-600 flex items-center gap-1';
                        syncBadge.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> Otomatis sinkron`;
                    }
                }
            }

            // Listen for radio level selection change
            levelRadios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    // Update styling visual border on radio cards
                    levelRadios.forEach(function (r) {
                        const card = r.closest('label');
                        if (!card) return;
                        if (r.checked) {
                            if (r.value === 'Safe') card.className = 'relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all border-emerald-600 bg-emerald-50/50';
                            if (r.value === 'Alert') card.className = 'relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all border-amber-500 bg-amber-50/50';
                            if (r.value === 'Emergency') card.className = 'relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all border-red-600 bg-red-50/50';
                        } else {
                            card.className = 'relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all border-slate-200 hover:border-slate-300';
                        }
                    });

                    // Automatically update text to follow the newly selected condition
                    applyLevelDefaults(this.value, false);
                });
            });

            // Button to force reset back to default preset
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    const checkedRadio = document.querySelector('input[name="level"]:checked');
                    if (checkedRadio) {
                        userHasCustomized = false;
                        applyLevelDefaults(checkedRadio.value, true);
                    }
                });
            }
        });
    </script>
@endpush


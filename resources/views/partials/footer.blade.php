<footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center font-bold">
                        K
                    </div>
                    <span class="font-bold text-white text-lg">Posko Tangguh Desa Kalikurmo</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Sistem Informasi Manajemen Kebencanaan, Pemetaan Zona Rawan Spasial, Pelaporan Kejadian Warga Terkompresi, dan Inventaris Logistik Pengungsian Desa Kalikurmo.
                </p>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Tautan Cepat</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Peta Interaktif Sebaran Kejadian</a></li>
                    <li><a href="{{ route('reports.create') }}" class="hover:text-white transition-colors">Formulir Pelaporan Kejadian Warga</a></li>
                    <li><a href="{{ route('home') }}#logistik" class="hover:text-white transition-colors">Ketersediaan Logistik Posko Evakuasi</a></li>
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Dashboard Komando &amp; Logistik (Admin)</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Kontak Darurat Posko</h3>
                <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-4 space-y-2 text-sm">
                    @forelse ($emergencyContacts ?? [] as $contact)
                        <p class="flex items-center justify-between gap-2">
                            <span class="text-slate-400 text-xs sm:text-sm">{{ $contact->name }}:</span>
                            <span class="font-mono font-semibold {{ $contact->color ?: 'text-white' }} text-xs sm:text-sm">
                                {{ $contact->number }}
                            </span>
                        </p>
                    @empty
                        <p class="flex items-center justify-between">
                            <span class="text-slate-400">Posko Balai Desa:</span>
                            <span class="font-mono font-semibold text-white">0812-3456-7890</span>
                        </p>
                        <p class="flex items-center justify-between">
                            <span class="text-slate-400">Call Center BPBD:</span>
                            <span class="font-mono font-semibold text-amber-400">112 / (0298) 555-119</span>
                        </p>
                        <p class="flex items-center justify-between">
                            <span class="text-slate-400">Puskesmas Kalikurmo:</span>
                            <span class="font-mono font-semibold text-emerald-400">0811-2233-4455</span>
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>&copy; {{ date('Y') }} Pemerintah Desa Kalikurmo &bull; Sistem Mitigasi &amp; Tanggap Darurat Bencana.</p>
            <p>Dioptimalkan untuk konsumsi penyimpanan rendah dengan kompresi citra otomatis.</p>
        </div>
    </div>
</footer>


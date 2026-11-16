<header class="bg-white/95 backdrop-blur border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand Logo & Village Name -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('icon.png') }}"
                     alt="Logo SI-TANGGUH Desa Kalikurmo"
                     class="w-10 h-10 rounded-xl object-cover shadow-sm border border-slate-200 group-hover:ring-2 group-hover:ring-red-600/30 transition-all">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-slate-900 tracking-tight text-base sm:text-lg">SI-TANGGUH</span>
                        <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            Desa Kalikurmo
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 hidden sm:block">Sistem Informasi Kebencanaan &amp; Mitigasi Warga</p>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}"
                   class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    Beranda &amp; Peta Rawan
                </a>
                <a href="{{ route('home') }}#logistik"
                   class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    Stok Logistik Posko
                </a>
                <a href="{{ route('home') }}#laporan-warga"
                   class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    Laporan Warga
                </a>
                <a href="{{ route('reports.create') }}"
                   class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('reports.*') ? 'bg-red-600 text-white' : 'text-red-600 hover:bg-red-50' }}">
                    + Buat Laporan
                </a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all {{ request()->routeIs('admin.*') || request()->routeIs('logistics.*') ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4 text-slate-500 {{ request()->routeIs('admin.*') ? 'text-white' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        <span>Panel Admin</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                title="Logout"
                                class="inline-flex items-center justify-center p-2 rounded-xl border border-slate-300 bg-white hover:bg-red-50 hover:text-red-600 hover:border-red-300 text-slate-600 text-xs sm:text-sm font-semibold transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all {{ request()->routeIs('login') ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span>Login Admin</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>


@php
    $currentLevel = $alertStatus->level ?? \App\Models\AlertStatus::LEVEL_SAFE;

    $bannerConfig = match ($currentLevel) {
        \App\Models\AlertStatus::LEVEL_EMERGENCY => [
            'wrapper' => 'bg-gradient-to-r from-red-700 via-red-600 to-rose-700 text-white border-b border-red-800',
            'badge' => 'bg-white text-red-700 ring-2 ring-red-200 animate-pulse',
            'badgeText' => 'SIAGA I • DARURAT',
            'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
        ],
        \App\Models\AlertStatus::LEVEL_ALERT => [
            'wrapper' => 'bg-gradient-to-r from-amber-600 via-amber-500 to-orange-600 text-white border-b border-amber-700',
            'badge' => 'bg-amber-950/90 text-amber-100 ring-1 ring-amber-300/40',
            'badgeText' => 'SIAGA II • WASPADA',
            'icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
        ],
        default => [
            'wrapper' => 'bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 text-white border-b border-emerald-900',
            'badge' => 'bg-emerald-950/60 text-emerald-100 ring-1 ring-emerald-400/30',
            'badgeText' => 'STATUS: AMAN TERKENDALI',
            'icon' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
        ],
    };
@endphp

<div class="{{ $bannerConfig['wrapper'] }} shadow-sm transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div class="flex items-start sm:items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold tracking-wider uppercase shrink-0 {{ $bannerConfig['badge'] }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $bannerConfig['icon'] }}" />
                    </svg>
                    {{ $bannerConfig['badgeText'] }}
                </span>

                <div class="text-xs sm:text-sm leading-snug">
                    <span class="font-bold">{{ $alertStatus->headline ?? 'Status Desa Kalikurmo: AMAN (Normal)' }}</span>
                    @if (!empty($alertStatus->message))
                        <span class="hidden sm:inline opacity-80 mx-1.5">—</span>
                        <span class="block sm:inline opacity-95">{{ $alertStatus->message }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between md:justify-end gap-4 text-xs opacity-90 shrink-0">
                <span>
                    Diperbarui: <strong>{{ $alertStatus->updated_at ? $alertStatus->updated_at->diffForHumans() : 'Baru saja' }}</strong>
                </span>
                <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-1 font-semibold underline decoration-white/60 hover:decoration-white">
                    Lapor Darurat &rarr;
                </a>
            </div>
        </div>
    </div>
</div>


@php
    $stripe = [
        'lime' => 'bg-[#C8F04B]',
        'pink' => 'bg-[#FF9EC4]',
        'blue' => 'bg-[#2F5BE8]',
    ];
@endphp

<x-filament-panels::page>
    {{-- Ringkasan minggu --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="lentur-stat">
            <span class="lentur-stat-label">Kelas</span>
            <span class="lentur-stat-value">{{ $summary['classes'] }}</span>
            <span class="lentur-stat-hint">{{ $summary['full'] }} penuh</span>
        </div>
        <div class="lentur-stat">
            <span class="lentur-stat-label">Keterisian</span>
            <span class="lentur-stat-value">{{ $summary['fill'] }}%</span>
            <span class="lentur-stat-hint">{{ $summary['booked'] }} dari {{ $summary['capacity'] }} tempat</span>
        </div>
        <div class="lentur-stat">
            <span class="lentur-stat-label">Pertama kali</span>
            <span class="lentur-stat-value">{{ $summary['firstTimers'] }}</span>
            <span class="lentur-stat-hint">sambut yang hangat</span>
        </div>
        <div class="lentur-stat">
            <span class="lentur-stat-label">Ada catatan</span>
            <span class="lentur-stat-value">{{ $summary['withNotes'] }}</span>
            <span class="lentur-stat-hint">cek sebelum kelas</span>
        </div>
    </div>

    {{-- Grid tujuh hari, bisa digeser di layar kecil --}}
    <div class="-mx-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:px-0">
        <div class="grid min-w-[56rem] grid-cols-7 gap-3">
            @foreach ($days as $day)
                <section @class([
                    'flex flex-col gap-2 rounded-3xl p-2',
                    'bg-[#C8F04B]/25 ring-2 ring-[#C8F04B]' => $day['isToday'],
                    'bg-gray-50 dark:bg-white/5' => ! $day['isToday'],
                ])>
                    <header class="px-2 pt-1">
                        <p class="lentur-display text-base capitalize text-gray-950 dark:text-white">{{ $day['label'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $day['date'] }}@if ($day['isToday']) · hari ini @endif
                        </p>
                    </header>

                    @forelse ($day['sessions'] as $session)
                        <a
                            href="{{ $session['url'] }}"
                            wire:navigate
                            @class([
                                'group relative flex flex-col gap-1.5 overflow-hidden rounded-2xl bg-white p-3 pl-4 shadow-sm ring-1 ring-gray-950/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-900 dark:ring-white/10',
                                'opacity-60' => in_array($session['state'], ['past', 'cancelled']),
                            ])
                        >
                            <span class="absolute inset-y-0 left-0 w-1.5 {{ $stripe[$session['color']] ?? $stripe['lime'] }}"></span>

                            <span class="flex items-center justify-between gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-semibold text-gray-950 dark:text-white">{{ $session['time'] }}</span>
                                @if ($session['state'] === 'full')
                                    <span class="rounded-full bg-[#FF9EC4] px-2 py-0.5 text-[0.65rem] font-semibold text-[#1F2A44]">Penuh</span>
                                @elseif ($session['state'] === 'cancelled')
                                    <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[0.65rem] font-semibold text-gray-700">Batal</span>
                                @endif
                            </span>

                            <span class="text-sm font-semibold leading-tight text-gray-950 dark:text-white">{{ $session['name'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $session['instructor'] }} · {{ $session['level'] }}</span>

                            @unless ($session['state'] === 'cancelled')
                                <span class="mt-1 flex items-center gap-2">
                                    <span class="h-2 flex-1 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                        <span
                                            @class([
                                                'block h-full rounded-full',
                                                'bg-[#FF9EC4]' => $session['state'] === 'full',
                                                'bg-[#2F5BE8]' => $session['state'] !== 'full',
                                            ])
                                            style="width: {{ $session['percent'] }}%"
                                        ></span>
                                    </span>
                                    <span class="text-xs tabular-nums text-gray-600 dark:text-gray-300">{{ $session['booked'] }}/{{ $session['capacity'] }}</span>
                                </span>
                            @endunless

                            @if ($session['firstTimers'] || $session['withNotes'])
                                <span class="flex flex-wrap gap-1 pt-0.5">
                                    @if ($session['firstTimers'])
                                        <span class="rounded-full bg-[#C8F04B] px-2 py-0.5 text-[0.65rem] font-semibold text-[#1F2A44]">{{ $session['firstTimers'] }} pertama kali</span>
                                    @endif
                                    @if ($session['withNotes'])
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[0.65rem] font-semibold text-amber-900">{{ $session['withNotes'] }} ada catatan</span>
                                    @endif
                                </span>
                            @endif
                        </a>
                    @empty
                        <p class="px-2 py-4 text-xs text-gray-400">Studio istirahat.</p>
                    @endforelse
                </section>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

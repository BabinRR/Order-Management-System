@props([
    'title' => 'Waiter',
    'heading' => '',
])

@php
    $openOrderCount = \App\Models\Order::query()
        ->whereIn('service_status', [
            \App\Models\Order::SERVICE_PENDING,
            \App\Models\Order::SERVICE_PREPARING,
        ])
        ->count();

    $tabs = [
        ['route' => 'waiter.dashboard', 'label' => 'My Tables', 'match' => 'waiter.dashboard', 'icon' => 'tables'],
        ['route' => 'waiter.orders.index', 'label' => 'Orders', 'match' => 'waiter.orders.*', 'icon' => 'orders', 'badge' => $openOrderCount],
        ['route' => 'waiter.bills.index', 'label' => 'Bills', 'match' => 'waiter.bills.*', 'icon' => 'bills'],
        ['route' => 'waiter.profile.edit', 'label' => 'Profile', 'match' => 'waiter.profile.*', 'icon' => 'profile'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name', 'Order Easy') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|syne:600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body
    class="admin-bg min-h-screen text-ink"
    x-data="{
        toast: @js(session('status')),
        now: '{{ now()->format('g:i A') }}',
        showToast(message) { this.toast = message; clearTimeout(this._t); this._t = setTimeout(() => this.toast = null, 3200); }
    }"
    x-init="
        if (toast) showToast(toast);
        setInterval(() => {
            const d = new Date();
            now = d.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        }, 30000);
    "
    x-on:toast.window="showToast($event.detail)"
>
    <div class="mx-auto min-h-screen max-w-7xl px-4 pb-10 pt-5 sm:px-6 lg:px-8">
        {{-- Top header --}}
        <header class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-[#a67c52] to-[#5d4037] font-display text-lg font-extrabold text-white shadow-sm">
                    {{ strtoupper(substr(config('app.name', 'OE'), 0, 1)) }}
                </div>
                <div>
                    <p class="font-display text-xl font-extrabold tracking-tight text-ink">{{ config('app.name', 'Order Easy') }}</p>
                    <p class="text-sm text-ink-soft/60">
                        {{ auth()->user()->name }}
                        <span class="text-ink-soft/35">·</span>
                        {{ auth()->user()->title ?: 'Waiter' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 rounded-2xl border border-[#d9cbb8] bg-white px-3.5 py-2.5 text-sm font-semibold text-ink shadow-sm">
                    <svg class="h-4 w-4 text-[#8b5e3c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="now">{{ now()->format('g:i A') }}</span>
                </div>
                <a
                    href="{{ route('waiter.orders.index', ['filter' => 'active']) }}"
                    class="relative flex h-11 w-11 items-center justify-center rounded-2xl border border-[#d9cbb8] bg-white text-ink shadow-sm transition hover:border-[#8b5e3c]"
                    aria-label="Open orders"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if ($openOrderCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#8b5e3c] px-1 text-[10px] font-bold text-white">{{ $openOrderCount > 9 ? '9+' : $openOrderCount }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-2xl border border-[#d9cbb8] bg-white px-3.5 py-2.5 text-xs font-semibold text-ink-soft shadow-sm hover:border-[#8b5e3c]">Sign out</button>
                </form>
            </div>
        </header>

        {{-- Pill tab nav --}}
        <nav class="mb-7 flex justify-center">
            <div class="inline-flex max-w-full flex-wrap items-center justify-center gap-1 rounded-[1.25rem] border border-[#d9cbb8]/80 bg-[#efe6da]/90 p-1.5 shadow-sm">
                @foreach ($tabs as $tab)
                    @php $active = request()->routeIs($tab['match']); @endphp
                    <a
                        href="{{ route($tab['route']) }}"
                        @class([
                            'relative inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition',
                            'bg-white text-[#8b5e3c] shadow-sm' => $active,
                            'text-ink-soft/65 hover:text-ink' => ! $active,
                        ])
                    >
                        @if ($tab['icon'] === 'tables')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h10M4 18h7"/></svg>
                        @elseif ($tab['icon'] === 'orders')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif ($tab['icon'] === 'bills')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        @else
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @endif
                        {{ $tab['label'] }}
                        @if (! empty($tab['badge']))
                            <span @class([
                                'flex h-5 min-w-5 items-center justify-center rounded-full px-1 text-[10px] font-bold',
                                'bg-[#8b5e3c] text-white' => $active,
                                'bg-[#8b5e3c]/15 text-[#8b5e3c]' => ! $active,
                            ])>{{ $tab['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </nav>

        @if ($heading)
            <h1 class="mb-5 font-display text-2xl font-extrabold tracking-tight text-ink">{{ $heading }}</h1>
        @endif

        {{ $slot }}
    </div>

    <div
        x-show="toast"
        x-cloak
        x-transition
        class="toast-slide fixed bottom-6 right-6 z-[60] flex max-w-sm items-center gap-3 rounded-2xl bg-ink px-4 py-3 text-sm font-medium text-white shadow-2xl ring-1 ring-white/10"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#8b5e3c]">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </span>
        <span x-text="toast"></span>
    </div>
</body>
</html>

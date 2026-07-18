<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'KitchenDesk') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|syne:600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body
    class="admin-bg min-h-screen text-ink"
    x-data="{
        sidebarOpen: false,
        toast: @js(session('status')),
        showToast(message) { this.toast = message; clearTimeout(this._t); this._t = setTimeout(() => this.toast = null, 3000); }
    }"
    x-init="if (toast) showToast(toast)"
    @toast.window="showToast($event.detail)"
>
    <div class="flex min-h-screen">
        {{-- Mobile overlay --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-ink/50 backdrop-blur-sm lg:hidden"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="sidebar-bg fixed inset-y-0 left-0 z-50 flex w-[264px] flex-col text-white shadow-2xl transition-transform duration-300 lg:static lg:translate-x-0 lg:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center gap-3 px-6 py-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-[#4f8462] to-[#2a4a37] font-display text-lg font-extrabold shadow-lg ring-1 ring-white/10">
                    K
                </div>
                <div>
                    <p class="font-display text-lg font-extrabold leading-none tracking-tight">KitchenDesk</p>
                    <p class="mt-1 text-[11px] font-medium uppercase tracking-[0.18em] text-white/40">Restaurant Admin</p>
                </div>
            </div>

            <div class="mx-4 mb-2 border-t border-white/10"></div>

            <nav class="flex flex-1 flex-col gap-1 px-4 py-3">
                <p class="px-3 pb-2 pt-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/35">Menu</p>
                @php
                    $links = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'chart'],
                        ['route' => 'admin.workers.index', 'label' => 'Workers', 'icon' => 'users'],
                        ['route' => 'admin.menu.index', 'label' => 'Food Menu', 'icon' => 'menu'],
                    ];
                @endphp

                @foreach ($links as $link)
                    @php $active = request()->routeIs($link['route']); @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        @class([
                            'group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-white/12 text-white shadow-sm' => $active,
                            'text-white/60 hover:bg-white/8 hover:text-white' => ! $active,
                        ])
                    >
                        @if ($active)
                            <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-[#7bbf95]"></span>
                        @endif
                        <span @class([
                            'flex h-8 w-8 items-center justify-center rounded-lg transition',
                            'bg-[#3d6b4f] text-white' => $active,
                            'bg-white/8 text-white/70 group-hover:bg-white/12' => ! $active,
                        ])>
                            @if ($link['icon'] === 'chart')
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h4v8H3v-8zm7-6h4v14h-4V7zm7 3h4v11h-4V10z"/></svg>
                            @elseif ($link['icon'] === 'users')
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm12 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            @else
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
                            @endif
                        </span>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="m-4 rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#3d6b4f] text-xs font-bold text-white">AD</div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-white/90">Admin</p>
                        <p class="truncate text-[11px] text-white/40">Owner</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex items-center justify-between gap-4 border-b border-[#d8e0da]/70 bg-[#f6f8f6]/80 px-4 py-3.5 backdrop-blur-md sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#d8e0da] bg-white text-ink shadow-sm lg:hidden"
                        aria-label="Open menu"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.15em] text-ink-soft/45">@yield('eyebrow', 'Overview')</p>
                        <h1 class="font-display text-xl font-extrabold tracking-tight text-ink sm:text-2xl">@yield('heading')</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="hidden items-center gap-2 rounded-xl border border-[#d8e0da] bg-white px-3 py-2 text-xs font-medium text-ink-soft/70 shadow-sm sm:flex">
                        <span class="h-2 w-2 rounded-full bg-[#3d6b4f]"></span>
                        {{ now()->format('D, M j') }}
                    </div>
                    <span class="rounded-xl bg-gradient-to-br from-[#3d6b4f] to-[#2a4a37] px-3 py-2 text-xs font-semibold text-white shadow-sm">Live</span>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Toast --}}
    <div
        x-show="toast"
        x-cloak
        x-transition
        class="toast-slide fixed bottom-6 right-6 z-[60] flex max-w-sm items-center gap-3 rounded-2xl bg-ink px-4 py-3 text-sm font-medium text-white shadow-2xl ring-1 ring-white/10"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#3d6b4f]">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </span>
        <span x-text="toast"></span>
    </div>
</body>
</html>

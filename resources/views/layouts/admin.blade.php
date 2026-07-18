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

        {{-- Desktop spacer so fixed sidebar does not cover content --}}
        <div class="hidden w-[264px] shrink-0 lg:block" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside
            class="sidebar-bg fixed inset-y-0 left-0 z-50 flex w-[264px] flex-col text-white shadow-2xl transition-transform duration-300 lg:translate-x-0 lg:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <div class="flex items-center gap-3 px-6 py-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-[#a67c52] to-[#5d4037] font-display text-lg font-extrabold shadow-lg ring-1 ring-white/10">
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
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'chart', 'match' => 'admin.dashboard'],
                        ['route' => 'admin.workers.index', 'label' => 'Workers', 'icon' => 'users', 'match' => 'admin.workers.*'],
                        ['route' => 'admin.menu.index', 'label' => 'Food Menu', 'icon' => 'menu', 'match' => 'admin.menu.*'],
                        ['route' => 'admin.profile.edit', 'label' => 'Settings', 'icon' => 'settings', 'match' => 'admin.profile.*'],
                    ];
                @endphp

                @foreach ($links as $link)
                    @php $active = request()->routeIs($link['match']); @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        @class([
                            'group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-white/12 text-white shadow-sm' => $active,
                            'text-white/60 hover:bg-white/8 hover:text-white' => ! $active,
                        ])
                    >
                        @if ($active)
                            <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-[#d4a574]"></span>
                        @endif
                        <span @class([
                            'flex h-8 w-8 items-center justify-center rounded-lg transition',
                            'bg-[#8b5e3c] text-white' => $active,
                            'bg-white/8 text-white/70 group-hover:bg-white/12' => ! $active,
                        ])>
                            @if ($link['icon'] === 'chart')
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h4v8H3v-8zm7-6h4v14h-4V7zm7 3h4v11h-4V10z"/></svg>
                            @elseif ($link['icon'] === 'users')
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm12 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            @elseif ($link['icon'] === 'settings')
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 000-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 000-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 000-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @else
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
                            @endif
                        </span>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            <a href="{{ route('admin.profile.edit') }}" class="m-4 block rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 transition hover:bg-white/8">
                <div class="flex items-center gap-3">
                    @if (auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover">
                    @else
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#8b5e3c] text-xs font-bold text-white">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-white/90">{{ auth()->user()->name }}</p>
                        <p class="truncate text-[11px] text-white/40">{{ auth()->user()->title ?: 'Admin' }}</p>
                    </div>
                </div>
            </a>
        </aside>

        {{-- Main --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex items-center justify-between gap-4 border-b border-[#d9cbb8]/70 bg-[#f7f0e8]/80 px-4 py-3.5 backdrop-blur-md sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#d9cbb8] bg-white text-ink shadow-sm lg:hidden"
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
                    <div class="hidden items-center gap-2 rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-xs font-medium text-ink-soft/70 shadow-sm sm:flex">
                        <span class="h-2 w-2 rounded-full bg-[#8b5e3c]"></span>
                        {{ now()->format('D, M j') }}
                    </div>
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
        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#8b5e3c]">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </span>
        <span x-text="toast"></span>
    </div>
</body>
</html>

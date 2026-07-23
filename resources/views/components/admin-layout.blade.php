@props([
    'title' => 'Control',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name', 'Order Easy') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|space-grotesk:500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .font-control { font-family: 'Space Grotesk', Manrope, ui-sans-serif, system-ui, sans-serif; }
        [x-cloak] { display: none !important; }

        /* Hard fallback so admin stays readable even if Vite CSS is stale */
        body.admin-control {
            background: #0a0a0a !important;
            color: #f5f5f5 !important;
        }
        body.admin-control .admin-header {
            background: rgba(10, 10, 10, 0.97);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }
        body.admin-control .admin-card,
        body.admin-control .admin-panel {
            background: #161616 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #f5f5f5 !important;
            border-radius: 1rem;
        }
        body.admin-control .admin-muted { color: rgba(255, 255, 255, 0.5) !important; }
        body.admin-control .admin-nav-pill {
            background: #141414;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        body.admin-control a,
        body.admin-control button,
        body.admin-control p,
        body.admin-control h1,
        body.admin-control h2,
        body.admin-control h3,
        body.admin-control span,
        body.admin-control td,
        body.admin-control th,
        body.admin-control label {
            color: inherit;
        }
        body.admin-control .text-white { color: #ffffff !important; }
        body.admin-control .text-black { color: #0a0a0a !important; }
        body.admin-control .admin-kpi-value { color: #ffffff !important; }
        body.admin-control .admin-table-empty {
            background: #1c1c1c !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: rgba(255, 255, 255, 0.4) !important;
        }
        body.admin-control .admin-table-occupied {
            background: rgba(14, 165, 233, 0.15) !important;
            border: 1px solid rgba(56, 189, 248, 0.55) !important;
            color: #7dd3fc !important;
        }
        body.admin-control .admin-table-attention {
            background: rgba(249, 115, 22, 0.15) !important;
            border: 1px solid rgba(251, 146, 60, 0.55) !important;
            color: #fdba74 !important;
        }
        body.admin-control input,
        body.admin-control select,
        body.admin-control textarea {
            background: #0f0f0f !important;
            color: #f5f5f5 !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
        }
        body.admin-control .rounded-2xl {
            background-color: #161616;
            border-color: rgba(255, 255, 255, 0.12);
            color: #f5f5f5;
        }
        body.admin-control table thead {
            color: rgba(255, 255, 255, 0.45);
        }
        body.admin-control table tbody {
            color: #f5f5f5;
        }
    </style>
</head>
<body
    class="admin-control min-h-screen antialiased"
    style="font-family: Manrope, ui-sans-serif, system-ui, sans-serif; background:#0a0a0a; color:#f5f5f5;"
    x-data="{
        toast: @js(session('status')),
        clock: '',
        dateLabel: '',
        tick() {
            const now = new Date();
            this.clock = now.toLocaleTimeString('en-GB', { hour12: false });
            this.dateLabel = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        },
        showToast(message) { this.toast = message; clearTimeout(this._t); this._t = setTimeout(() => this.toast = null, 3000); }
    }"
    x-init="tick(); setInterval(() => tick(), 1000); if (toast) showToast(toast)"
    x-on:toast.window="showToast($event.detail)"
>
    @php
        $nav = [
            ['route' => 'admin.dashboard', 'label' => 'Floor', 'match' => 'admin.dashboard'],
            ['route' => 'admin.workers.index', 'label' => 'Workers', 'match' => 'admin.workers.*'],
            ['route' => 'admin.attendance.index', 'label' => 'Attendance', 'match' => 'admin.attendance.*'],
            ['route' => 'admin.menu.index', 'label' => 'Menu', 'match' => 'admin.menu.*'],
            ['route' => 'admin.profile.edit', 'label' => 'Profile', 'match' => 'admin.profile.*'],
        ];
    @endphp

    <header class="admin-header sticky top-0 z-40 backdrop-blur-md">
        <div class="mx-auto flex max-w-[1400px] items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3" style="color:#f5f5f5; text-decoration:none;">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg" style="background:#e11d48;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v7a2 2 0 002 2h0a2 2 0 002-2V4M9 4v16M15 8c0 0 2-1 2-3s-1-3-2-3v14"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-control text-sm font-bold uppercase tracking-[0.12em] sm:text-base" style="color:#ffffff;">{{ config('app.name', 'Order Easy') }} Control</p>
                        <p class="admin-muted text-[11px] font-medium">Main Service Floor</p>
                    </div>
                </a>
            </div>

            <div class="hidden text-center md:block">
                <p class="font-control text-2xl font-semibold tabular-nums tracking-wide" style="color:#ffffff;" x-text="clock">{{ now()->format('H:i:s') }}</p>
                <p class="admin-muted text-xs font-medium" x-text="dateLabel">{{ now()->format('D, M j') }}</p>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <nav class="admin-nav-pill hidden items-center gap-1 rounded-full p-1 lg:flex">
                    @foreach ($nav as $item)
                        @php $active = request()->routeIs($item['match']); @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition"
                            style="{{ $active ? 'background:#ffffff; color:#0a0a0a;' : 'color:rgba(255,255,255,0.6);' }}"
                        >{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                @auth
                    <a href="{{ route('admin.profile.edit') }}" class="admin-nav-pill flex items-center gap-2.5 rounded-full py-1.5 pl-1.5 pr-3 transition" style="color:#f5f5f5; text-decoration:none;">
                        @if (auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold" style="background:rgba(225,29,72,0.25); color:#fb7185;">
                                {{ auth()->user()->initials }}
                            </div>
                        @endif
                        <div class="hidden min-w-0 sm:block">
                            <p class="truncate text-xs font-semibold" style="color:#ffffff;">{{ auth()->user()->name }}</p>
                            <p class="admin-muted truncate text-[10px]">{{ auth()->user()->title ?: 'Floor Manager' }}</p>
                        </div>
                    </a>
                @endauth
            </div>
        </div>

        <div class="flex gap-1 overflow-x-auto border-t px-4 py-2 lg:hidden" style="border-color:rgba(255,255,255,0.08);">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="shrink-0 rounded-full px-3 py-1.5 text-xs font-semibold"
                    style="{{ $active ? 'background:#ffffff; color:#0a0a0a;' : 'background:#141414; color:rgba(255,255,255,0.6);' }}"
                >{{ $item['label'] }}</a>
            @endforeach
        </div>
    </header>

    <main class="mx-auto max-w-[1400px] px-4 py-6 sm:px-6 lg:px-8" style="color:#f5f5f5;">
        {{ $slot }}
    </main>

    <div
        x-show="toast"
        x-cloak
        x-transition
        class="fixed bottom-6 right-6 z-[60] flex max-w-sm items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium shadow-2xl"
        style="background:#161616; border:1px solid rgba(255,255,255,0.12); color:#ffffff;"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-full" style="background:rgba(16,185,129,0.2); color:#34d399;">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </span>
        <span x-text="toast"></span>
    </div>
</body>
</html>

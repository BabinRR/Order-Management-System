<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Order') — {{ config('app.name', 'KitchenDesk') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|syne:600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body
    class="admin-bg min-h-screen text-ink"
    x-data="{
        toast: @js(session('status')),
        showToast(message) { this.toast = message; clearTimeout(this._t); this._t = setTimeout(() => this.toast = null, 3200); }
    }"
    x-init="if (toast) showToast(toast)"
>
    <header class="sticky top-0 z-30 border-b border-[#d9cbb8]/80 bg-[#f7f0e8]/90 backdrop-blur-md">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6">
            <a href="{{ route('customer.home') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-[#a67c52] to-[#5d4037] font-display text-base font-extrabold text-white shadow-sm">
                    K
                </div>
                <div>
                    <p class="font-display text-lg font-extrabold leading-none tracking-tight">KitchenDesk</p>
                    <p class="mt-0.5 text-[11px] font-medium uppercase tracking-[0.16em] text-ink-soft/45">Guest Order</p>
                </div>
            </a>

            <nav class="flex items-center gap-1.5 sm:gap-2">
                @if (session('customer_table'))
                    <span class="hidden rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-xs font-semibold text-ink-soft sm:inline-flex">
                        Table {{ session('customer_table') }}
                    </span>
                    <a href="{{ route('customer.menu') }}" @class(['rounded-xl px-3 py-2 text-sm font-semibold transition', 'bg-[#8b5e3c] text-white' => request()->routeIs('customer.menu'), 'text-ink-soft hover:bg-white' => ! request()->routeIs('customer.menu')])>Menu</a>
                    <a href="{{ route('customer.cart') }}" @class(['relative rounded-xl px-3 py-2 text-sm font-semibold transition', 'bg-[#8b5e3c] text-white' => request()->routeIs('customer.cart'), 'text-ink-soft hover:bg-white' => ! request()->routeIs('customer.cart')])>
                        Cart
                        @php $cartBadge = \App\Support\Cart::count(); @endphp
                        @if ($cartBadge > 0)
                            <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#a0522d] px-1 text-[10px] font-bold text-white">{{ $cartBadge }}</span>
                        @endif
                    </a>
                    <a href="{{ route('customer.bill') }}" @class(['rounded-xl px-3 py-2 text-sm font-semibold transition', 'bg-[#8b5e3c] text-white' => request()->routeIs('customer.bill'), 'text-ink-soft hover:bg-white' => ! request()->routeIs('customer.bill')])>Bill</a>
                @endif
                <a href="{{ route('login') }}" class="rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-xs font-semibold text-ink-soft hover:border-[#8b5e3c]">Staff</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-[#a0522d]/30 bg-[#f5e6d8] px-4 py-3 text-sm text-[#a0522d]">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>

    <div
        x-show="toast"
        x-cloak
        x-transition
        class="toast-slide fixed bottom-6 right-6 z-[60] flex max-w-sm items-center gap-3 rounded-2xl bg-ink px-4 py-3 text-sm font-medium text-white shadow-2xl"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#8b5e3c]">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </span>
        <span x-text="toast"></span>
    </div>
</body>
</html>

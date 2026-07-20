@props([
    'title' => 'Order',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name', 'Order Easy') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600,700|manrope:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .font-serif-display { font-family: 'Cormorant Garamond', ui-serif, Georgia, serif; }
        .customer-cream {
            background-color: #f9f7f2;
            background-image:
                radial-gradient(ellipse 80% 50% at 0% 0%, rgba(139, 94, 60, 0.06), transparent),
                radial-gradient(ellipse 60% 40% at 100% 100%, rgba(93, 64, 55, 0.05), transparent);
        }
    </style>
</head>
<body
    class="customer-cream min-h-screen text-ink antialiased"
    style="font-family: Manrope, ui-sans-serif, system-ui, sans-serif;"
    x-data="{
        toast: @js(session('status')),
        showToast(message) { this.toast = message; clearTimeout(this._t); this._t = setTimeout(() => this.toast = null, 3200); },
        callWaiter() { this.showToast('A waiter has been notified for your table.'); }
    }"
    x-init="if (toast) showToast(toast)"
>
    <x-header />

    <main class="mx-auto max-w-6xl px-4 pb-12 pt-6 sm:px-6 sm:pt-8">
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-[#a0522d]/25 bg-[#f5e6d8] px-4 py-3 text-sm text-[#a0522d]">
                {{ $errors->first() }}
            </div>
        @endif

        {{ $slot }}
    </main>

    <div
        x-show="toast"
        x-cloak
        x-transition
        class="toast-slide fixed bottom-6 right-6 z-[60] flex max-w-sm items-center gap-3 rounded-2xl bg-[#2c2118] px-4 py-3 text-sm font-medium text-white shadow-2xl"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#8b5e3c]">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </span>
        <span x-text="toast"></span>
    </div>
</body>
</html>

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
    <x-header />

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-[#a0522d]/30 bg-[#f5e6d8] px-4 py-3 text-sm text-[#a0522d]">
                {{ $errors->first() }}
            </div>
        @endif

        {{ $slot }}
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

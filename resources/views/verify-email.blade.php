<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email — {{ config('app.name', 'Order Easy') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|syne:600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-bg min-h-screen text-ink">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-2xl border border-[#d9cbb8] bg-white p-6 shadow-[0_8px_30px_rgba(26,31,28,0.06)] sm:p-8">
            <h1 class="font-display text-2xl font-extrabold tracking-tight text-ink">Verify your email</h1>
            <p class="mt-3 text-sm leading-relaxed text-ink-soft/70">
                We sent a verification link to
                <span class="font-semibold text-ink">{{ auth()->user()->email }}</span>.
                Open that link to continue using {{ config('app.name', 'Order Easy') }}.
            </p>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-[#ede0d0] px-4 py-3 text-sm text-[#5d4037]">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-3 text-sm font-semibold text-white shadow-sm">
                    Resend verification email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-[#d9cbb8] px-4 py-3 text-sm font-semibold text-ink-soft hover:bg-[#f7f0e8]">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in — {{ config('app.name', 'KitchenDesk') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|syne:600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-bg min-h-screen text-ink">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#a67c52] to-[#5d4037] font-display text-2xl font-extrabold text-white shadow-lg">
                    K
                </div>
                <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink">KitchenDesk</h1>
                <p class="mt-2 text-sm text-ink-soft/65">Sign in to manage your restaurant</p>
            </div>

            <div class="rounded-2xl border border-[#d9cbb8] bg-white p-6 shadow-[0_8px_30px_rgba(26,31,28,0.06)] sm:p-8">
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', 'admin@kitchen.local') }}"
                            required
                            autofocus
                            autocomplete="username"
                            @class([
                                'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                                'border-[#a0522d]' => $errors->has('email'),
                                'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('email'),
                            ])
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-[#a0522d]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            @class([
                                'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                                'border-[#a0522d]' => $errors->has('password'),
                                'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('password'),
                            ])
                        >
                        @error('password')
                            <p class="mt-1.5 text-xs font-medium text-[#a0522d]">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2 text-sm text-ink-soft/70">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#d9cbb8] text-[#8b5e3c] focus:ring-[#8b5e3c]/30">
                        Remember me
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
                    >
                        Sign in
                    </button>
                </form>

                <p class="mt-5 space-y-2 rounded-xl bg-[#f7f0e8] px-3 py-2.5 text-center text-xs text-ink-soft/60">
                    <span class="block">Admin: <span class="font-semibold text-ink">admin@kitchen.local</span> / <span class="font-semibold text-ink">password</span></span>
                    <span class="block">Waiter: <span class="font-semibold text-ink">waiter@kitchen.local</span> / <span class="font-semibold text-ink">password</span></span>
                    <a href="{{ route('customer.home') }}" class="mt-1 inline-block font-semibold text-[#8b5e3c] hover:underline">Customer ordering →</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

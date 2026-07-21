<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset password — {{ config('app.name', 'Order Easy') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:600,700|manrope:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased" style="font-family: Manrope, ui-sans-serif, system-ui, sans-serif; background:#f7f5f2;">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-2xl border border-[#e5dfd6] bg-white p-6 shadow-sm sm:p-8">
            <h1 class="font-serif text-3xl font-semibold tracking-tight text-[#1a1a1a]" style="font-family:'Cormorant Garamond', Georgia, serif;">
                Set a new password
            </h1>
            <p class="mt-2 text-sm text-[#6b6b6b]">
                Choose a strong password for your {{ config('app.name') }} account.
            </p>

            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="mb-1.5 block text-xs font-semibold text-[#4a4a4a]">Email Address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        required
                        autocomplete="username"
                        class="w-full rounded-xl border bg-white px-3.5 py-3 text-sm outline-none"
                        style="border-color: {{ $errors->has('email') ? '#7b352e' : '#ddd6ce' }};"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium" style="color:#7b352e;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-xs font-semibold text-[#4a4a4a]">New password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border bg-white px-3.5 py-3 text-sm outline-none"
                        style="border-color: {{ $errors->has('password') ? '#7b352e' : '#ddd6ce' }};"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium" style="color:#7b352e;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold text-[#4a4a4a]">Confirm password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border bg-white px-3.5 py-3 text-sm outline-none"
                        style="border-color:#ddd6ce;"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl px-4 py-3.5 text-sm font-semibold text-white shadow-sm"
                    style="background:#7b352e;"
                >
                    Reset password
                </button>
            </form>
        </div>
    </div>
</body>
</html>

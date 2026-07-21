<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Set password — {{ config('app.name', 'Order Easy') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:600,700|manrope:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased" style="font-family: Manrope, ui-sans-serif, system-ui, sans-serif; background:#f7f5f2;">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-2xl border border-[#e5dfd6] bg-white p-6 shadow-sm sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em]" style="color:#7b352e;">First login</p>
            <h1 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-[#1a1a1a]" style="font-family:'Cormorant Garamond', Georgia, serif;">
                Choose your password
            </h1>
            <p class="mt-2 text-sm text-[#6b6b6b]">
                Hi {{ $user->name }} — enter the verification code sent to <strong>{{ $user->email }}</strong>, then set your new password.
            </p>

            <form method="POST" action="{{ route('password.force.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="code" class="mb-1.5 block text-xs font-semibold text-[#4a4a4a]">Email verification code</label>
                    <input
                        id="code"
                        type="text"
                        name="code"
                        value="{{ old('code') }}"
                        required
                        autocomplete="one-time-code"
                        placeholder="Code from Gmail"
                        class="w-full rounded-xl border bg-white px-3.5 py-3 text-sm uppercase tracking-widest outline-none"
                        style="border-color: {{ $errors->has('code') ? '#7b352e' : '#ddd6ce' }};"
                    >
                    @error('code')
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
                    Save password & continue
                </button>
            </form>
        </div>
    </div>
</body>
</html>

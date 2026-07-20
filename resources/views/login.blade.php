<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in — {{ config('app.name', 'Order Easy') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600,700,500i,600i|manrope:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .font-serif-login { font-family: 'Cormorant Garamond', ui-serif, Georgia, serif; }
        .login-left {
            background:
                radial-gradient(ellipse 80% 60% at 20% 0%, rgba(196, 165, 116, 0.12), transparent),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(123, 53, 46, 0.35), transparent),
                linear-gradient(165deg, #3a2420 0%, #2d1b17 45%, #1a100e 100%);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body
    class="min-h-screen antialiased"
    style="font-family: Manrope, ui-sans-serif, system-ui, sans-serif; background:#f6f4f1; color:#1a1a1a;"
    x-data="{
        role: 'admin',
        showPassword: false,
        labels: {
            admin: { button: 'Sign In to Admin Portal', hint: 'Full system control and analytics' },
            waiter: { button: 'Sign In to Waiter Portal', hint: 'Tables, orders, and bills' },
            customer: { button: 'Continue to Customer Order', hint: 'Browse the menu and order from your table' }
        }
    }"
>
    <div class="flex min-h-screen">
        {{-- Left branding panel --}}
        <aside class="login-left relative hidden w-[46%] flex-col justify-between px-10 py-10 text-white lg:flex xl:px-14">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full shadow-lg" style="background:#7b352e;">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v7a2 2 0 002 2h0a2 2 0 002-2V4M9 4v16M15 8c0 0 2-1 2-3s-1-3-2-3v14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-serif-login text-3xl font-semibold leading-none tracking-tight text-white">Order</p>
                        <p class="font-serif-login text-2xl italic leading-none" style="color:#c5a070;">Easy</p>
                    </div>
                </div>
                <p class="mt-4 text-sm" style="color:rgba(255,255,255,0.45);">Crafted with care. Served with precision.</p>
            </div>

            <div class="my-10 space-y-3">
                <div class="rounded-2xl px-5 py-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08);">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl" style="background:rgba(123,53,46,0.45); color:#e8b4ae;">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Admin</p>
                            <p class="mt-1 text-xs leading-relaxed" style="color:rgba(255,255,255,0.5);">Full system control, comprehensive analytics, and menu management.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl px-5 py-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08);">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl" style="background:rgba(123,53,46,0.45); color:#e8b4ae;">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Waiter</p>
                            <p class="mt-1 text-xs leading-relaxed" style="color:rgba(255,255,255,0.5);">Table management, rapid order taking, and real-time kitchen sync.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl px-5 py-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08);">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl" style="background:rgba(123,53,46,0.45); color:#e8b4ae;">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Customer</p>
                            <p class="mt-1 text-xs leading-relaxed" style="color:rgba(255,255,255,0.5);">Browse the digital menu, request service, and place your orders seamlessly.</p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-xs" style="color:rgba(255,255,255,0.3);">{{ config('app.name', 'Order Easy') }} OMS</p>
        </aside>

        {{-- Right form panel --}}
        <section class="flex flex-1 flex-col justify-center px-6 py-10 sm:px-10 lg:px-16 xl:px-24" style="background:#f7f5f2;">
            <div class="mx-auto w-full max-w-md">
                {{-- Mobile brand --}}
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full text-white" style="background:#7b352e;">
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v7a2 2 0 002 2h0a2 2 0 002-2V4M9 4v16M15 8c0 0 2-1 2-3s-1-3-2-3v14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-serif-login text-2xl font-semibold leading-none text-[#1a1a1a]">Order <span class="italic" style="color:#7b352e;">Easy</span></p>
                    </div>
                </div>

                <h1 class="font-serif-login text-4xl font-semibold tracking-tight text-[#1a1a1a] sm:text-5xl">Welcome back</h1>
                <p class="mt-2 text-sm text-[#6b6b6b]">Sign in to continue to your dashboard</p>

                {{-- Role tabs --}}
                <div class="mt-8 flex rounded-xl p-1" style="background:#ebe7e2;">
                    <template x-for="item in [
                        { id: 'admin', label: 'Admin' },
                        { id: 'waiter', label: 'Waiter' },
                        { id: 'customer', label: 'Customer' }
                    ]" :key="item.id">
                        <button
                            type="button"
                            @click="role = item.id"
                            class="flex-1 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
                            :style="role === item.id
                                ? 'background:#ffffff; color:#1a1a1a; box-shadow:0 1px 3px rgba(0,0,0,0.08);'
                                : 'background:transparent; color:#6b6b6b;'"
                            x-text="item.label"
                        ></button>
                    </template>
                </div>

                <p class="mt-3 text-xs text-[#8a8a8a]" x-text="labels[role].hint"></p>

                {{-- Staff login --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    class="mt-6 space-y-5"
                    x-show="role !== 'customer'"
                    x-cloak
                >
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-semibold text-[#4a4a4a]">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@restaurant.com"
                            class="w-full rounded-xl border bg-white px-3.5 py-3 text-sm outline-none transition"
                            style="border-color: {{ $errors->has('email') ? '#7b352e' : '#ddd6ce' }};"
                            onfocus="this.style.borderColor='#7b352e'; this.style.boxShadow='0 0 0 3px rgba(123,53,46,0.12)'"
                            onblur="this.style.borderColor='{{ $errors->has('email') ? '#7b352e' : '#ddd6ce' }}'; this.style.boxShadow='none'"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium" style="color:#7b352e;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="password" class="text-xs font-semibold text-[#4a4a4a]">Password</label>
                            <span class="text-xs font-semibold" style="color:#7b352e;" title="Ask your administrator to reset your password">Forgot password?</span>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-xl border bg-white px-3.5 py-3 pr-11 text-sm outline-none transition"
                                style="border-color: {{ $errors->has('password') ? '#7b352e' : '#ddd6ce' }};"
                                onfocus="this.style.borderColor='#7b352e'; this.style.boxShadow='0 0 0 3px rgba(123,53,46,0.12)'"
                                onblur="this.style.borderColor='{{ $errors->has('password') ? '#7b352e' : '#ddd6ce' }}'; this.style.boxShadow='none'"
                            >
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9a9a9a] hover:text-[#4a4a4a]"
                                @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                            >
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs font-medium" style="color:#7b352e;">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2.5 text-sm text-[#4a4a4a]">
                        <input
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-[#ddd6ce]"
                            style="accent-color:#7b352e;"
                        >
                        Keep me signed in
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-xl px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
                        style="background:#7b352e;"
                        x-text="labels[role].button"
                    >Sign In to Admin Portal</button>
                </form>

                {{-- Customer CTA --}}
                <div class="mt-6 space-y-5" x-show="role === 'customer'" x-cloak>
                    <div class="rounded-2xl border border-[#e5dfd6] bg-white px-5 py-6">
                        <p class="font-serif-login text-2xl font-semibold text-[#1a1a1a]">Ready to order?</p>
                        <p class="mt-2 text-sm leading-relaxed text-[#6b6b6b]">
                            No staff account needed. Pick your table, browse the menu, and send orders straight to the kitchen.
                        </p>
                    </div>
                    <a
                        href="{{ route('customer.home') }}"
                        class="flex w-full items-center justify-center rounded-xl px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
                        style="background:#7b352e;"
                    >
                        Continue to Customer Order
                    </a>
                </div>

                <p class="mt-8 text-center text-sm text-[#6b6b6b]">
                    Need account access?
                    <span class="font-semibold" style="color:#7b352e;">Contact system administrator</span>
                </p>
                <p class="mt-4 text-center text-[11px] text-[#9a9a9a]">{{ config('app.name', 'Order Easy') }} OMS</p>
            </div>
        </section>
    </div>
</body>
</html>

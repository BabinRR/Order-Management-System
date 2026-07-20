@php
    $cartCount = \App\Support\Cart::count();
    $cartTotal = \App\Support\Cart::total();
    $table = session('customer_table');
@endphp

<header class="sticky top-0 z-30 border-b border-[#e8e2d8]/90 bg-[#f9f7f2]/92 backdrop-blur-md">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <a href="{{ $table ? route('customer.menu') : route('customer.home') }}" class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-full border border-[#d9cbb8] bg-white text-[#8b5e3c] shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v7a2 2 0 002 2h0a2 2 0 002-2V4M9 4v16M15 8c0 0 2-1 2-3s-1-3-2-3v14"/>
                </svg>
            </div>
            <div>
                <p class="font-serif-display text-2xl font-semibold leading-none tracking-tight text-ink">{{ config('app.name', 'Order Easy') }}</p>
                @if ($table)
                    <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-ink-soft/45">Table {{ $table }}</p>
                @else
                    <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-ink-soft/45">Guest order</p>
                @endif
            </div>
        </a>

        <div class="flex items-center gap-2 sm:gap-3">
            @if ($table)
                <button
                    type="button"
                    x-on:click="callWaiter()"
                    class="inline-flex items-center gap-2 rounded-full border border-[#d9cbb8] bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-soft shadow-sm transition hover:border-[#8b5e3c]"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="hidden sm:inline">Call Waiter</span>
                </button>
                <a
                    href="{{ route('customer.cart') }}"
                    class="inline-flex items-center gap-2 rounded-full bg-[#3e2723] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#5d4037]"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 7h14m-8-7v7m4-7v7"/></svg>
                    <span>Rs {{ number_format($cartTotal) }}</span>
                    @if ($cartCount > 0)
                        <span class="rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('customer.bill') }}" class="hidden rounded-full border border-[#d9cbb8] bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-soft shadow-sm transition hover:border-[#8b5e3c] sm:inline-flex">
                    Bill
                </a>
            @else
                <a href="{{ route('login') }}" class="rounded-full border border-[#d9cbb8] bg-white px-4 py-2.5 text-sm font-semibold text-ink-soft shadow-sm hover:border-[#8b5e3c]">Staff</a>
            @endif
        </div>
    </div>
</header>

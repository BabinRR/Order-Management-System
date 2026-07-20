<header class="sticky top-0 z-30 border-b border-[#d9cbb8]/80 bg-[#f7f0e8]/90 backdrop-blur-md">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6">
        <a href="{{ route('customer.home') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-[#a67c52] to-[#5d4037] font-display text-base font-extrabold text-white shadow-sm">
                K
            </div>
            <div>
                <p class="font-display text-lg font-extrabold leading-none tracking-tight">{{ config('app.name', 'Order Easy') }}</p>
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

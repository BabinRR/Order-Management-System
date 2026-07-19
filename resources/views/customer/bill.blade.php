@extends('layouts.customer')

@section('title', 'Your Bill')

@section('content')
<div class="mx-auto max-w-2xl space-y-5">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.15em] text-ink-soft/45">Table {{ $table }}</p>
            <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink">Your bill</h1>
            @if ($customerName)
                <p class="mt-1 text-sm text-ink-soft/60">For {{ $customerName }}</p>
            @endif
        </div>
        <a href="{{ route('customer.menu') }}" class="text-sm font-semibold text-[#8b5e3c] hover:underline">Order more</a>
    </div>

    @if ($orders->isEmpty())
        <div class="rounded-2xl border border-dashed border-[#d9cbb8] bg-white/60 px-6 py-14 text-center">
            <p class="text-sm text-ink-soft/60">No orders yet for this table.</p>
            <a href="{{ route('customer.menu') }}" class="mt-4 inline-flex rounded-xl bg-[#8b5e3c] px-4 py-2.5 text-sm font-semibold text-white">Browse menu</a>
        </div>
    @else
        <section class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(44,33,24,0.04)]">
            <div class="border-b border-[#f0e6da] px-5 py-4">
                <h2 class="font-display text-lg font-extrabold text-ink">Open items</h2>
                <p class="text-sm text-ink-soft/60">Unpaid balance · Rs {{ number_format($unpaidTotal) }}</p>
            </div>

            <ul class="divide-y divide-[#f0e6da]">
                @forelse ($unpaidOrders as $order)
                    <li class="flex items-start justify-between gap-3 px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="font-semibold text-ink">{{ $order->menuItem?->name ?? 'Item' }}</p>
                            <p class="text-xs text-ink-soft/55">
                                {{ $order->reference }} · qty {{ $order->items_count }}
                                · <span class="capitalize">{{ $order->service_status }}</span>
                            </p>
                        </div>
                        <p class="shrink-0 font-bold text-[#8b5e3c]">Rs {{ number_format($order->total) }}</p>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-ink-soft/55">All caught up — nothing unpaid.</li>
                @endforelse
            </ul>

            @if ($unpaidOrders->isNotEmpty())
                <div class="border-t border-[#f0e6da] px-5 py-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm font-semibold text-ink-soft">Total due</span>
                        <span class="font-display text-2xl font-extrabold text-[#8b5e3c]">Rs {{ number_format($unpaidTotal) }}</span>
                    </div>

                    <form method="POST" action="{{ route('customer.bill.pay') }}" class="space-y-4">
                        @csrf
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Pay with</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($methods as $method)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="{{ $method }}" class="peer sr-only" @checked(old('payment_method', 'cash') === $method) required>
                                        <span class="flex items-center justify-center rounded-xl border border-[#d9cbb8] bg-[#f7f0e8] px-3 py-3 text-sm font-semibold capitalize text-ink-soft transition peer-checked:border-[#8b5e3c] peer-checked:bg-[#8b5e3c] peer-checked:text-white">
                                            {{ $method }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:opacity-95">
                            Complete payment · Rs {{ number_format($unpaidTotal) }}
                        </button>
                    </form>
                </div>
            @endif
        </section>

        @if ($paidOrders->isNotEmpty())
            <section class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white">
                <div class="border-b border-[#f0e6da] px-5 py-4">
                    <h2 class="font-display text-lg font-extrabold text-ink">Paid</h2>
                </div>
                <ul class="divide-y divide-[#f0e6da]">
                    @foreach ($paidOrders as $order)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div>
                                <p class="font-medium text-ink">{{ $order->menuItem?->name ?? 'Item' }}</p>
                                <p class="text-xs capitalize text-ink-soft/50">{{ $order->payment_method }} · {{ $order->paid_at?->format('g:i A') }}</p>
                            </div>
                            <p class="font-semibold text-ink-soft">Rs {{ number_format($order->total) }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif
</div>
@endsection

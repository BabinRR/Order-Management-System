<x-customer-layout title="Your Bill">
<div class="mx-auto max-w-2xl space-y-6 pt-2">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <section>
            <h1 class="font-serif-display text-4xl font-semibold tracking-tight text-ink">Your bill.</h1>
            <p class="mt-2 text-sm text-ink-soft/60">
                Table {{ $table }}
                @if ($customerName)
                    · {{ $customerName }}
                @endif
            </p>
        </section>
        <a href="{{ route('customer.menu') }}" class="text-sm font-semibold text-[#8b5e3c] hover:underline">Order more</a>
    </div>

    @if ($orders->isEmpty())
        <div class="rounded-[1.5rem] border border-dashed border-[#d9cbb8] bg-white/70 px-6 py-16 text-center">
            <p class="font-serif-display text-2xl font-medium text-ink-soft/50">No orders yet.</p>
            <p class="mt-2 text-sm text-ink-soft/55">Place something from the menu to see it here.</p>
            <a href="{{ route('customer.menu') }}" class="mt-6 inline-flex rounded-full bg-[#3e2723] px-5 py-2.5 text-sm font-semibold text-white">Browse menu</a>
        </div>
    @else
        <section class="overflow-hidden rounded-[1.5rem] border border-[#ebe4d8] bg-white shadow-[0_2px_12px_rgba(44,33,24,0.04)]">
            <div class="border-b border-[#f3ebe0] px-5 py-5">
                <h2 class="font-serif-display text-2xl font-semibold text-ink">Open items</h2>
                <p class="mt-1 text-sm text-ink-soft/60">Unpaid balance · Rs {{ number_format($unpaidTotal) }}</p>
            </div>

            <ul class="divide-y divide-[#f3ebe0]">
                @forelse ($unpaidOrders as $order)
                    <li class="flex items-start justify-between gap-3 px-5 py-4">
                        <div class="min-w-0">
                            <p class="font-serif-display text-lg font-semibold text-ink">{{ $order->menuItem?->name ?? 'Item' }}</p>
                            <p class="text-xs text-ink-soft/55">
                                {{ $order->reference }} · qty {{ $order->items_count }}
                                · <span class="capitalize">{{ $order->service_status }}</span>
                            </p>
                        </div>
                        <p class="shrink-0 font-serif-display text-lg font-semibold text-ink">Rs {{ number_format($order->total) }}</p>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-ink-soft/55">All caught up — nothing unpaid.</li>
                @endforelse
            </ul>

            @if ($unpaidOrders->isNotEmpty())
                <div class="border-t border-[#f3ebe0] px-5 py-6">
                    <div class="mb-5 flex items-center justify-between">
                        <span class="text-sm font-semibold text-ink-soft">Total due</span>
                        <span class="font-serif-display text-3xl font-semibold text-ink">Rs {{ number_format($unpaidTotal) }}</span>
                    </div>

                    <form method="POST" action="{{ route('customer.bill.pay') }}" class="space-y-4">
                        @csrf
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Pay with</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($methods as $method)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="{{ $method }}" class="peer sr-only" @checked(old('payment_method', 'cash') === $method) required>
                                        <span class="flex items-center justify-center rounded-full border border-[#e0d8cc] bg-[#f9f7f2] px-3 py-3 text-sm font-semibold capitalize text-ink-soft transition peer-checked:border-[#3e2723] peer-checked:bg-[#3e2723] peer-checked:text-white">
                                            {{ $method }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="w-full rounded-full bg-[#3e2723] px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#5d4037]">
                            Complete payment · Rs {{ number_format($unpaidTotal) }}
                        </button>
                    </form>
                </div>
            @endif
        </section>

        @if ($paidOrders->isNotEmpty())
            <section class="overflow-hidden rounded-[1.5rem] border border-[#ebe4d8] bg-white">
                <div class="border-b border-[#f3ebe0] px-5 py-5">
                    <h2 class="font-serif-display text-2xl font-semibold text-ink">Paid</h2>
                </div>
                <ul class="divide-y divide-[#f3ebe0]">
                    @foreach ($paidOrders as $order)
                        <li class="flex items-center justify-between gap-3 px-5 py-4 text-sm">
                            <div>
                                <p class="font-serif-display text-lg font-medium text-ink">{{ $order->menuItem?->name ?? 'Item' }}</p>
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
</x-customer-layout>

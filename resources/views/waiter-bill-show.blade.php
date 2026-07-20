<x-waiter-layout title="Table {{ $table }} Bill" heading="Table {{ $table }} Check">
<div class="mx-auto max-w-2xl space-y-5">
    <a href="{{ route('waiter.bills.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#8b5e3c] hover:underline">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to bills
    </a>

    <section class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
        <div class="border-b border-[#f0e6da] bg-[#f7f0e8]/80 px-5 py-4 sm:px-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="font-display text-2xl font-extrabold text-ink">Table {{ $table }}</p>
                    <p class="mt-1 text-sm text-ink-soft/60">{{ $orders->count() }} order line{{ $orders->count() === 1 ? '' : 's' }}</p>
                </div>
                <span @class([
                    'rounded-lg px-2.5 py-1 text-xs font-semibold',
                    'bg-[#f5e6d8] text-[#a0522d]' => $unpaidOrders->isNotEmpty(),
                    'bg-[#ede0d0] text-[#5d4037]' => $unpaidOrders->isEmpty(),
                ])>{{ $unpaidOrders->isNotEmpty() ? 'Unpaid' : 'Paid' }}</span>
            </div>
        </div>

        <div class="px-5 py-5 sm:px-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#f0e6da] text-left text-xs uppercase tracking-wider text-ink-soft/50">
                        <th class="pb-2 font-semibold">Item</th>
                        <th class="pb-2 text-right font-semibold">Qty</th>
                        <th class="pb-2 text-right font-semibold">Status</th>
                        <th class="pb-2 text-right font-semibold">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-b border-[#f0e6da]">
                            <td class="py-3">
                                <p class="font-medium text-ink">{{ $order->menuItem?->name ?? 'Menu item' }}</p>
                                <p class="text-xs text-ink-soft/50">{{ $order->reference }}</p>
                            </td>
                            <td class="py-3 text-right text-ink-soft">{{ $order->items_count }}</td>
                            <td class="py-3 text-right text-xs capitalize text-ink-soft/60">
                                {{ $order->service_status }} / {{ $order->payment_status }}
                            </td>
                            <td class="py-3 text-right font-semibold text-ink">Rs {{ number_format($order->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="pt-4 text-right text-sm font-semibold text-ink-soft">
                            {{ $unpaidOrders->isNotEmpty() ? 'Total due' : 'Total paid' }}
                        </td>
                        <td class="pt-4 text-right font-display text-2xl font-extrabold text-[#8b5e3c]">
                            Rs {{ number_format($unpaidOrders->isNotEmpty() ? $unpaidTotal : $orders->sum('total')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if ($unpaidOrders->isNotEmpty())
                <form method="POST" action="{{ route('waiter.bills.table.pay', $table) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Payment method</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($methods as $method)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $method }}" class="peer sr-only" @checked(old('payment_method', 'cash') === $method) required>
                                    <span class="flex items-center justify-center rounded-xl border border-[#d9cbb8] bg-white px-3 py-3 text-sm font-semibold capitalize text-ink-soft transition peer-checked:border-[#8b5e3c] peer-checked:bg-[#8b5e3c] peer-checked:text-white">
                                        {{ $method }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_method') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                        Collect Rs {{ number_format($unpaidTotal) }} for table {{ $table }}
                    </button>
                </form>
            @else
                <div class="mt-6 rounded-xl bg-[#ede0d0] px-4 py-3 text-sm text-[#5d4037]">
                    This table’s bill is fully paid.
                </div>
            @endif
        </div>
    </section>

    <a href="{{ route('waiter.orders.index', ['table' => $table]) }}" class="inline-flex text-sm font-semibold text-[#8b5e3c] hover:underline">
        View table {{ $table }} orders →
    </a>
</div>
</x-waiter-layout>

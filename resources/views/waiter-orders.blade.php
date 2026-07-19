<x-waiter-layout title="Orders" eyebrow="Service" heading="Orders by Table">
@php
    $filters = [
        'active' => 'Active',
        'pending' => 'Pending',
        'preparing' => 'Preparing',
        'served' => 'Served',
    ];
@endphp
<div class="space-y-5">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
            @foreach ($filters as $key => $label)
                <a
                    href="{{ route('waiter.orders.index', array_filter(['filter' => $key, 'table' => $activeTable ?: null])) }}"
                    @class([
                        'rounded-xl px-3.5 py-2 text-sm font-semibold transition',
                        'bg-[#8b5e3c] text-white shadow-sm' => $filter === $key,
                        'border border-[#d9cbb8] bg-white text-ink-soft hover:border-[#8b5e3c]' => $filter !== $key,
                    ])
                >{{ $label }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('waiter.orders.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <label for="table" class="text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Table</label>
            <select
                id="table"
                name="table"
                onchange="this.form.submit()"
                class="rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-sm font-medium outline-none focus:border-[#8b5e3c]"
            >
                <option value="">All tables</option>
                @foreach ($availableTables as $tableNumber)
                    <option value="{{ $tableNumber }}" @selected($activeTable === (string) $tableNumber)>Table {{ $tableNumber }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @forelse ($tables as $table)
        <section class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
            <div class="flex flex-col gap-3 border-b border-[#f0e6da] bg-[#f7f0e8]/80 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-display text-xl font-extrabold text-ink">Table {{ $table->table_number }}</h2>
                    <p class="mt-0.5 text-sm text-ink-soft/60">
                        {{ $table->orders->count() }} order{{ $table->orders->count() === 1 ? '' : 's' }}
                        · {{ $table->item_count }} items
                        · Rs {{ number_format($table->total) }}
                    </p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @if ($table->pending_count)
                            <span class="rounded-lg bg-[#f5e6d8] px-2 py-0.5 text-[11px] font-semibold text-[#a0522d]">{{ $table->pending_count }} pending</span>
                        @endif
                        @if ($table->preparing_count)
                            <span class="rounded-lg bg-[#ede0d0] px-2 py-0.5 text-[11px] font-semibold text-[#5d4037]">{{ $table->preparing_count }} preparing</span>
                        @endif
                        @if ($table->served_count)
                            <span class="rounded-lg bg-[#e8f0e4] px-2 py-0.5 text-[11px] font-semibold text-[#3d5a3a]">{{ $table->served_count }} served</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($table->has_open_service)
                        <form method="POST" action="{{ route('waiter.orders.table.served', $table->table_number) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-xl bg-[#8b5e3c] px-3.5 py-2 text-xs font-semibold text-white">Mark all served</button>
                        </form>
                    @endif
                    @if ($table->unpaid_total > 0)
                        <a href="{{ route('waiter.bills.table', $table->table_number) }}" class="rounded-xl bg-[#a0522d] px-3.5 py-2 text-xs font-semibold text-white">
                            Bill · Rs {{ number_format($table->unpaid_total) }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-white text-xs uppercase tracking-wider text-ink-soft/45">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Item</th>
                            <th class="px-5 py-3 font-semibold">Qty</th>
                            <th class="px-5 py-3 font-semibold">Amount</th>
                            <th class="px-5 py-3 font-semibold">Service</th>
                            <th class="px-5 py-3 font-semibold">Payment</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0e6da]">
                        @foreach ($table->orders as $order)
                            <tr class="hover:bg-[#f7f0e8]/50">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-ink">{{ $order->menuItem?->name ?? 'Item' }}</p>
                                    <p class="text-xs text-ink-soft/50">{{ $order->reference }}</p>
                                </td>
                                <td class="px-5 py-3 text-ink-soft">{{ $order->items_count }}</td>
                                <td class="px-5 py-3 font-medium text-ink">Rs {{ number_format($order->total) }}</td>
                                <td class="px-5 py-3">
                                    <span @class([
                                        'inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold capitalize',
                                        'bg-[#f5e6d8] text-[#a0522d]' => $order->service_status === 'pending',
                                        'bg-[#ede0d0] text-[#5d4037]' => $order->service_status === 'preparing',
                                        'bg-[#e8f0e4] text-[#3d5a3a]' => $order->service_status === 'served',
                                    ])>{{ $order->service_status }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <span @class([
                                        'inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold capitalize',
                                        'bg-[#f5e6d8] text-[#a0522d]' => $order->payment_status === 'unpaid',
                                        'bg-[#ede0d0] text-[#5d4037]' => $order->payment_status === 'paid',
                                    ])>{{ $order->payment_status }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @if ($order->service_status === 'pending')
                                            <form method="POST" action="{{ route('waiter.orders.preparing', $order) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-[#d9cbb8] bg-white px-3 py-1.5 text-xs font-semibold text-ink hover:border-[#8b5e3c]">Preparing</button>
                                            </form>
                                        @endif
                                        @if (in_array($order->service_status, ['pending', 'preparing'], true))
                                            <form method="POST" action="{{ route('waiter.orders.served', $order) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg bg-[#8b5e3c] px-3 py-1.5 text-xs font-semibold text-white">Served</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @empty
        <p class="rounded-2xl border border-dashed border-[#d9cbb8] bg-white/50 py-16 text-center text-sm text-ink-soft/55">
            No orders for this view.
        </p>
    @endforelse
</div>
</x-waiter-layout>

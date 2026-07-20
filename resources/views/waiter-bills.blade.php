<x-waiter-layout title="Bills" heading="Bills & Checks">
<div class="space-y-5">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('waiter.bills.index', array_filter(['filter' => 'unpaid', 'table' => $activeTable ?: null])) }}" @class(['rounded-xl px-3.5 py-2 text-sm font-semibold transition', 'bg-[#8b5e3c] text-white shadow-sm' => $filter === 'unpaid', 'border border-[#d9cbb8] bg-white text-ink-soft' => $filter !== 'unpaid'])>Unpaid</a>
            <a href="{{ route('waiter.bills.index', array_filter(['filter' => 'paid', 'table' => $activeTable ?: null])) }}" @class(['rounded-xl px-3.5 py-2 text-sm font-semibold transition', 'bg-[#8b5e3c] text-white shadow-sm' => $filter === 'paid', 'border border-[#d9cbb8] bg-white text-ink-soft' => $filter !== 'paid'])>Paid</a>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if ($filter === 'unpaid')
                <p class="rounded-xl bg-[#f5e6d8] px-3.5 py-2 text-sm font-semibold text-[#a0522d]">
                    Outstanding: Rs {{ number_format($unpaidTotal) }}
                </p>
            @endif
            <form method="GET" action="{{ route('waiter.bills.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <select name="table" onchange="this.form.submit()" class="rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-sm font-medium outline-none focus:border-[#8b5e3c]">
                    <option value="">All tables</option>
                    @foreach ($availableTables as $tableNumber)
                        <option value="{{ $tableNumber }}" @selected($activeTable === (string) $tableNumber)>Table {{ $tableNumber }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @forelse ($tables as $table)
            <article class="flex flex-col rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-extrabold text-ink">Table {{ $table->table_number }}</h2>
                        <p class="mt-0.5 text-sm text-ink-soft/60">
                            {{ $table->orders->count() }} line{{ $table->orders->count() === 1 ? '' : 's' }} · {{ $table->item_count }} items
                        </p>
                    </div>
                    <span @class([
                        'rounded-lg px-2.5 py-1 text-xs font-semibold',
                        'bg-[#f5e6d8] text-[#a0522d]' => $table->unpaid,
                        'bg-[#ede0d0] text-[#5d4037]' => ! $table->unpaid,
                    ])>{{ $table->unpaid ? 'Unpaid' : 'Paid' }}</span>
                </div>

                <ul class="mt-4 space-y-2">
                    @foreach ($table->orders->take(5) as $order)
                        <li class="flex items-center justify-between gap-3 rounded-xl bg-[#f7f0e8] px-3 py-2 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-ink">{{ $order->menuItem?->name ?? 'Item' }}</p>
                                <p class="text-xs capitalize text-ink-soft/50">{{ $order->service_status }} · qty {{ $order->items_count }}</p>
                            </div>
                            <p class="shrink-0 font-semibold text-[#8b5e3c]">Rs {{ number_format($order->total) }}</p>
                        </li>
                    @endforeach
                    @if ($table->orders->count() > 5)
                        <li class="text-center text-xs text-ink-soft/50">+{{ $table->orders->count() - 5 }} more items</li>
                    @endif
                </ul>

                <div class="mt-4 flex items-end justify-between border-t border-[#f0e6da] pt-4">
                    <p class="font-display text-xl font-extrabold text-[#8b5e3c]">Rs {{ number_format($table->total) }}</p>
                    <a href="{{ route('waiter.bills.table', $table->table_number) }}" class="rounded-lg bg-[#8b5e3c] px-3 py-1.5 text-xs font-semibold text-white">
                        {{ $table->unpaid ? 'Collect pay' : 'View bill' }}
                    </a>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-[#d9cbb8] bg-white/50 py-16 text-center text-sm text-ink-soft/55">
                No {{ $filter }} bills found.
            </p>
        @endforelse
    </div>
</div>
</x-waiter-layout>

<x-waiter-layout title="Waiter Dashboard" eyebrow="Waiter" heading="Dashboard">
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-soft/50">Pending</p>
            <p class="mt-2 font-display text-3xl font-extrabold text-ink">{{ $pending }}</p>
            <p class="mt-1 text-sm text-ink-soft/55">Awaiting kitchen</p>
        </div>
        <div class="rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-soft/50">Preparing</p>
            <p class="mt-2 font-display text-3xl font-extrabold text-ink">{{ $preparing }}</p>
            <p class="mt-1 text-sm text-ink-soft/55">In the kitchen</p>
        </div>
        <div class="rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-soft/50">Unpaid bills</p>
            <p class="mt-2 font-display text-3xl font-extrabold text-[#a0522d]">{{ $servedUnpaid }}</p>
            <p class="mt-1 text-sm text-ink-soft/55">Served, need payment</p>
        </div>
        <div class="rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-soft/50">Paid today</p>
            <p class="mt-2 font-display text-3xl font-extrabold text-[#5d4037]">{{ $paidToday }}</p>
            <p class="mt-1 text-sm font-semibold text-[#8b5e3c]">Rs {{ number_format($revenueToday) }}</p>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        <section class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
            <div class="flex items-center justify-between border-b border-[#f0e6da] px-5 py-4">
                <div>
                    <h2 class="font-display text-lg font-extrabold text-ink">Active by table</h2>
                    <p class="text-sm text-ink-soft/60">Pending & preparing</p>
                </div>
                <a href="{{ route('waiter.orders.index') }}" class="text-sm font-semibold text-[#8b5e3c] hover:underline">View all</a>
            </div>
            <ul class="divide-y divide-[#f0e6da]">
                @forelse ($activeByTable as $table)
                    <li class="px-5 py-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-ink">Table {{ $table->table_number }}</p>
                                <p class="truncate text-sm text-ink-soft/60">
                                    {{ $table->orders->pluck('menuItem.name')->filter()->take(3)->implode(', ') }}
                                    @if ($table->orders->count() > 3)…@endif
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-xs font-semibold text-ink-soft/55">{{ $table->item_count }} items</p>
                                <a href="{{ route('waiter.orders.index', ['table' => $table->table_number]) }}" class="text-xs font-semibold text-[#8b5e3c] hover:underline">Open</a>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-ink-soft/55">No active kitchen orders.</li>
                @endforelse
            </ul>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
            <div class="flex items-center justify-between border-b border-[#f0e6da] px-5 py-4">
                <div>
                    <h2 class="font-display text-lg font-extrabold text-ink">Bills by table</h2>
                    <p class="text-sm text-ink-soft/60">Served & unpaid</p>
                </div>
                <a href="{{ route('waiter.bills.index') }}" class="text-sm font-semibold text-[#8b5e3c] hover:underline">View all</a>
            </div>
            <ul class="divide-y divide-[#f0e6da]">
                @forelse ($unpaidByTable as $table)
                    <li class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="font-semibold text-ink">Table {{ $table->table_number }}</p>
                            <p class="truncate text-sm text-ink-soft/60">{{ $table->item_count }} items · Rs {{ number_format($table->total) }}</p>
                        </div>
                        <a href="{{ route('waiter.bills.table', $table->table_number) }}" class="shrink-0 rounded-lg bg-[#8b5e3c] px-3 py-1.5 text-xs font-semibold text-white">Pay</a>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-ink-soft/55">No unpaid bills right now.</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
</x-waiter-layout>

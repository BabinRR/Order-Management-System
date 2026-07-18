@extends('layouts.waiter')

@section('title', 'Bills & Payments')
@section('eyebrow', 'Payments')
@section('heading', 'Bills & Pay')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('waiter.bills.index', ['filter' => 'unpaid']) }}" @class(['rounded-xl px-3.5 py-2 text-sm font-semibold transition', 'bg-[#8b5e3c] text-white shadow-sm' => $filter === 'unpaid', 'border border-[#d9cbb8] bg-white text-ink-soft' => $filter !== 'unpaid'])>Unpaid</a>
            <a href="{{ route('waiter.bills.index', ['filter' => 'paid']) }}" @class(['rounded-xl px-3.5 py-2 text-sm font-semibold transition', 'bg-[#8b5e3c] text-white shadow-sm' => $filter === 'paid', 'border border-[#d9cbb8] bg-white text-ink-soft' => $filter !== 'paid'])>Paid</a>
        </div>
        @if ($filter === 'unpaid')
            <p class="rounded-xl bg-[#f5e6d8] px-3.5 py-2 text-sm font-semibold text-[#a0522d]">
                Outstanding: Rs {{ number_format($unpaidTotal) }}
            </p>
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($bills as $bill)
            <article class="flex flex-col rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-display text-lg font-extrabold text-ink">{{ $bill->reference }}</p>
                        <p class="mt-0.5 text-sm text-ink-soft/60">Table {{ $bill->table_number }}</p>
                    </div>
                    <span @class([
                        'rounded-lg px-2.5 py-1 text-xs font-semibold capitalize',
                        'bg-[#f5e6d8] text-[#a0522d]' => $bill->payment_status === 'unpaid',
                        'bg-[#ede0d0] text-[#5d4037]' => $bill->payment_status === 'paid',
                    ])>{{ $bill->payment_status }}</span>
                </div>

                <p class="mt-3 text-sm text-ink-soft/70">{{ $bill->menuItem?->name ?? 'Order' }} · {{ $bill->items_count }} items</p>
                <p class="mt-1 text-xs capitalize text-ink-soft/50">Service: {{ $bill->service_status }}</p>

                <div class="mt-4 flex items-end justify-between border-t border-[#f0e6da] pt-4">
                    <p class="font-display text-xl font-extrabold text-[#8b5e3c]">Rs {{ number_format($bill->total) }}</p>
                    <a href="{{ route('waiter.bills.show', $bill) }}" class="rounded-lg bg-[#8b5e3c] px-3 py-1.5 text-xs font-semibold text-white">
                        {{ $bill->payment_status === 'paid' ? 'View bill' : 'Collect pay' }}
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
@endsection

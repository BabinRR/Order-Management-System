@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Overview')
@section('heading', 'Dashboard')

@section('content')
@php
    $maxRevenue = max(1, ...array_column($weeklyRevenue, 'amount'));
    $cards = [
        [
            'label' => 'Revenue today',
            'value' => 'Rs '.number_format($stats['revenue_today']),
            'delta' => $stats['revenue_today_delta'],
            'sub' => 'vs yesterday',
            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m0-8c1.11 0 2.08.402 2.599 1M12 16c-1.11 0-2.08-.402-2.599-1',
            'tone' => 'wood',
        ],
        [
            'label' => 'Revenue this week',
            'value' => 'Rs '.number_format($stats['revenue_week']),
            'delta' => $stats['revenue_week_delta'],
            'sub' => 'vs last week',
            'icon' => 'M3 13h4v8H3v-8zm7-6h4v14h-4V7zm7 3h4v11h-4V10z',
            'tone' => 'ember',
        ],
        [
            'label' => 'Orders today',
            'value' => number_format($stats['orders_today']),
            'delta' => null,
            'sub' => number_format($stats['orders_week']).' this week',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'tone' => 'wood',
        ],
        [
            'label' => 'Avg. order value',
            'value' => 'Rs '.number_format($stats['avg_order'], 0),
            'delta' => null,
            'sub' => $stats['active_workers'].' staff active',
            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
            'tone' => 'ember',
        ],
    ];
@endphp

<div class="space-y-6">
    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $index => $card)
            <div class="fade-up rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)]" style="animation-delay: {{ $index * 0.06 }}s">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-soft/50">{{ $card['label'] }}</p>
                    <span @class([
                        'flex h-9 w-9 items-center justify-center rounded-xl',
                        'bg-[#ede0d0] text-[#5d4037]' => $card['tone'] === 'wood',
                        'bg-[#f5e6d8] text-[#a0522d]' => $card['tone'] === 'ember',
                    ])>
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </span>
                </div>
                <p class="mt-3 font-display text-[1.7rem] font-extrabold leading-none tracking-tight text-ink">{{ $card['value'] }}</p>
                <div class="mt-2.5 flex items-center gap-1.5 text-sm">
                    @if (! is_null($card['delta']))
                        @php $up = $card['delta'] >= 0; @endphp
                        <span @class([
                            'inline-flex items-center gap-0.5 rounded-md px-1.5 py-0.5 text-xs font-bold',
                            'bg-[#ede0d0] text-[#5d4037]' => $up,
                            'bg-[#f5e6d8] text-[#a0522d]' => ! $up,
                        ])>
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $up ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                            </svg>
                            {{ abs($card['delta']) }}%
                        </span>
                    @endif
                    <span class="text-ink-soft/55">{{ $card['sub'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-4 xl:grid-cols-5">
        {{-- Weekly revenue chart --}}
        <section class="fade-up xl:col-span-3 rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)] sm:p-6" style="animation-delay: 0.12s">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <h2 class="font-display text-lg font-extrabold text-ink">Weekly revenue</h2>
                    <p class="mt-0.5 text-sm text-ink-soft/60">Last 7 days sales performance</p>
                </div>
                <div class="text-right">
                    <p class="font-display text-xl font-extrabold text-[#8b5e3c]">Rs {{ number_format($stats['revenue_week']) }}</p>
                    <p class="text-xs text-ink-soft/55">total this week</p>
                </div>
            </div>

            <div class="flex h-56 items-end gap-2 sm:gap-3">
                @foreach ($weeklyRevenue as $index => $day)
                    @php $height = max(4, ($day['amount'] / $maxRevenue) * 100); @endphp
                    <div class="group flex flex-1 flex-col items-center gap-2">
                        <div class="relative flex w-full flex-1 items-end">
                            <div
                                class="bar-fill relative w-full rounded-t-lg bg-gradient-to-t from-[#5d4037] to-[#b8956c] transition-all duration-300 group-hover:from-[#a0522d] group-hover:to-[#c4895a]"
                                style="height: {{ $height }}%; animation-delay: {{ $index * 0.06 }}s"
                            >
                                <span class="pointer-events-none absolute -top-7 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-lg bg-ink px-2 py-1 text-[10px] font-semibold text-white opacity-0 shadow-lg transition group-hover:opacity-100">
                                    Rs {{ number_format($day['amount']) }}
                                </span>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-ink-soft/60">{{ $day['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Top items --}}
        <section class="fade-up xl:col-span-2 rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)] sm:p-6" style="animation-delay: 0.18s">
            <h2 class="font-display text-lg font-extrabold text-ink">Top sellers</h2>
            <p class="mt-0.5 text-sm text-ink-soft/60">By revenue this week</p>

            <ul class="mt-5 space-y-2.5">
                @forelse ($topItems as $rank => $item)
                    <li class="flex items-center gap-3 rounded-xl border border-transparent bg-[#f7f0e8] px-3 py-2.5 transition hover:border-[#d9cbb8]">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-bold text-ink-soft/70 shadow-sm">{{ $rank + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $item['name'] }}</p>
                            <p class="text-xs text-ink-soft/55">{{ $item['orders'] }} orders</p>
                        </div>
                        <p class="shrink-0 text-sm font-bold text-[#8b5e3c]">Rs {{ number_format($item['revenue']) }}</p>
                    </li>
                @empty
                    <li class="rounded-xl bg-[#f7f0e8] px-3 py-8 text-center text-sm text-ink-soft/55">No sales recorded yet.</li>
                @endforelse
            </ul>
        </section>
    </div>

    {{-- Recent orders --}}
    <section class="fade-up overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]" style="animation-delay: 0.24s">
        <div class="flex items-center justify-between border-b border-[#f0e6da] px-5 py-4 sm:px-6">
            <div>
                <h2 class="font-display text-lg font-extrabold text-ink">Recent orders</h2>
                <p class="mt-0.5 text-sm text-ink-soft/60">Latest service activity</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-[#f7f0e8] text-xs uppercase tracking-wider text-ink-soft/50">
                    <tr>
                        <th class="px-5 py-3 font-semibold sm:px-6">Order</th>
                        <th class="px-5 py-3 font-semibold sm:px-6">Table</th>
                        <th class="px-5 py-3 font-semibold sm:px-6">Items</th>
                        <th class="px-5 py-3 font-semibold sm:px-6">Total</th>
                        <th class="px-5 py-3 font-semibold sm:px-6">Status</th>
                        <th class="px-5 py-3 font-semibold sm:px-6">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0e6da]">
                    @forelse ($recentOrders as $order)
                        <tr class="transition hover:bg-[#f7f0e8]/70">
                            <td class="px-5 py-3.5 font-semibold text-ink sm:px-6">{{ $order->reference }}</td>
                            <td class="px-5 py-3.5 text-ink-soft sm:px-6">{{ $order->table_number }}</td>
                            <td class="px-5 py-3.5 text-ink-soft sm:px-6">{{ $order->items_count }}</td>
                            <td class="px-5 py-3.5 font-medium text-ink sm:px-6">Rs {{ number_format($order->total) }}</td>
                            <td class="px-5 py-3.5 sm:px-6">
                                @php
                                    $statusClass = match ($order->status) {
                                        'Completed' => 'bg-[#ede0d0] text-[#5d4037]',
                                        'Preparing' => 'bg-[#f5e6d8] text-[#a0522d]',
                                        default => 'bg-[#f0e6da] text-ink-soft',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-ink-soft/60 sm:px-6">{{ $order->created_at->format('M j, g:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-ink-soft/55">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@extends('layouts.waiter')

@section('title', 'Orders')
@section('eyebrow', 'Service')
@section('heading', 'Orders')

@section('content')
@php
    $filters = [
        'active' => 'Active',
        'pending' => 'Pending',
        'preparing' => 'Preparing',
        'served' => 'Served',
    ];
@endphp
<div class="space-y-5">
    <div class="flex flex-wrap gap-2">
        @foreach ($filters as $key => $label)
            <a
                href="{{ route('waiter.orders.index', ['filter' => $key]) }}"
                @class([
                    'rounded-xl px-3.5 py-2 text-sm font-semibold transition',
                    'bg-[#8b5e3c] text-white shadow-sm' => $filter === $key,
                    'border border-[#d9cbb8] bg-white text-ink-soft hover:border-[#8b5e3c]' => $filter !== $key,
                ])
            >{{ $label }}</a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-[#f7f0e8] text-xs uppercase tracking-wider text-ink-soft/50">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold">Order</th>
                        <th class="px-5 py-3.5 font-semibold">Table</th>
                        <th class="px-5 py-3.5 font-semibold">Items</th>
                        <th class="px-5 py-3.5 font-semibold">Total</th>
                        <th class="px-5 py-3.5 font-semibold">Service</th>
                        <th class="px-5 py-3.5 font-semibold">Payment</th>
                        <th class="px-5 py-3.5 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0e6da]">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-[#f7f0e8]/70">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-ink">{{ $order->reference }}</p>
                                <p class="text-xs text-ink-soft/55">{{ $order->menuItem?->name ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-ink-soft">{{ $order->table_number }}</td>
                            <td class="px-5 py-3.5 text-ink-soft">{{ $order->items_count }}</td>
                            <td class="px-5 py-3.5 font-medium text-ink">Rs {{ number_format($order->total) }}</td>
                            <td class="px-5 py-3.5">
                                <span @class([
                                    'inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold capitalize',
                                    'bg-[#f5e6d8] text-[#a0522d]' => $order->service_status === 'pending',
                                    'bg-[#ede0d0] text-[#5d4037]' => $order->service_status === 'preparing',
                                    'bg-[#e8f0e4] text-[#3d5a3a]' => $order->service_status === 'served',
                                ])>{{ $order->service_status }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span @class([
                                    'inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold capitalize',
                                    'bg-[#f5e6d8] text-[#a0522d]' => $order->payment_status === 'unpaid',
                                    'bg-[#ede0d0] text-[#5d4037]' => $order->payment_status === 'paid',
                                ])>{{ $order->payment_status }}</span>
                            </td>
                            <td class="px-5 py-3.5">
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
                                    @if ($order->payment_status === 'unpaid')
                                        <a href="{{ route('waiter.bills.show', $order) }}" class="rounded-lg bg-[#a0522d] px-3 py-1.5 text-xs font-semibold text-white">Bill</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center text-sm text-ink-soft/55">No orders in this view.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

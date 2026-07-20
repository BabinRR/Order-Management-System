<x-admin-layout title="Control Floor">
@php
    $orderDelta = $stats['orders_delta'];
    $revenueDelta = $stats['revenue_delta'];
@endphp

<div class="space-y-6" x-data="{ filter: 'all' }" style="color:#f5f5f5;">
    {{-- KPI cards --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="admin-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="admin-muted text-xs font-medium">Total Orders</p>
                    <p class="font-control admin-kpi-value mt-2 text-3xl font-semibold tracking-tight">{{ number_format($stats['orders_today']) }}</p>
                    @if (! is_null($orderDelta))
                        <p class="mt-2 text-xs font-semibold" style="color: {{ $orderDelta >= 0 ? '#34d399' : '#fb7185' }};">
                            {{ $orderDelta >= 0 ? '+' : '' }}{{ $orderDelta }}%
                        </p>
                    @else
                        <p class="admin-muted mt-2 text-xs">vs yesterday</p>
                    @endif
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-full" style="background:rgba(255,255,255,0.06); color:#fbbf24;">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
        </div>

        <div class="admin-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="admin-muted text-xs font-medium">Revenue</p>
                    <p class="font-control admin-kpi-value mt-2 text-3xl font-semibold tracking-tight">Rs {{ number_format($stats['revenue_today']) }}</p>
                    @if (! is_null($revenueDelta))
                        <p class="mt-2 text-xs font-semibold" style="color: {{ $revenueDelta >= 0 ? '#34d399' : '#fb7185' }};">
                            {{ $revenueDelta >= 0 ? '+' : '' }}{{ $revenueDelta }}%
                        </p>
                    @else
                        <p class="admin-muted mt-2 text-xs">today</p>
                    @endif
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-full" style="background:rgba(16,185,129,0.12); color:#34d399;">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2"/></svg>
                </span>
            </div>
        </div>

        <div class="admin-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="admin-muted text-xs font-medium">Active Tables</p>
                    <p class="font-control admin-kpi-value mt-2 text-3xl font-semibold tracking-tight">{{ $stats['active_tables'] }}/{{ $stats['table_total'] }}</p>
                    <p class="admin-muted mt-2 text-xs">on the floor</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-full" style="background:rgba(14,165,233,0.12); color:#38bdf8;">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4v7a2 2 0 002 2h0a2 2 0 002-2V4M9 4v16M15 8c0 0 2-1 2-3s-1-3-2-3v14"/></svg>
                </span>
            </div>
        </div>

        <div class="admin-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="admin-muted text-xs font-medium">Avg Ticket Time</p>
                    <p class="font-control admin-kpi-value mt-2 text-3xl font-semibold tracking-tight">{{ $stats['avg_ticket_minutes'] }}m</p>
                    <p class="admin-muted mt-2 text-xs">kitchen pace</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-full" style="background:rgba(249,115,22,0.12); color:#fb923c;">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-5">
        <section class="admin-panel xl:col-span-3 overflow-hidden">
            <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between" style="border-bottom:1px solid rgba(255,255,255,0.1);">
                <div class="flex items-center gap-2">
                    <h2 class="font-control text-lg font-semibold" style="color:#ffffff;">Active Orders</h2>
                    <span class="rounded-full px-2 py-0.5 text-xs font-bold" style="background:rgba(255,255,255,0.12); color:rgba(255,255,255,0.8);">{{ $activeOrders->count() }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="filter = 'all'" :style="filter === 'all' ? 'background:#ffffff;color:#0a0a0a;' : 'background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.55);'" class="rounded-full px-3 py-1.5 text-xs font-semibold transition">All</button>
                    <button type="button" @click="filter = 'pending'" :style="filter === 'pending' ? 'background:#f97316;color:#ffffff;' : 'background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.55);'" class="rounded-full px-3 py-1.5 text-xs font-semibold transition">Pending</button>
                    <button type="button" @click="filter = 'preparing'" :style="filter === 'preparing' ? 'background:#0ea5e9;color:#ffffff;' : 'background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.55);'" class="rounded-full px-3 py-1.5 text-xs font-semibold transition">In Progress</button>
                    <button type="button" @click="filter = 'ready'" :style="filter === 'ready' ? 'background:#10b981;color:#ffffff;' : 'background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.55);'" class="rounded-full px-3 py-1.5 text-xs font-semibold transition">Ready</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead style="color:rgba(255,255,255,0.4);">
                        <tr class="text-[11px] uppercase tracking-wider">
                            <th class="px-5 py-3 font-medium">Order</th>
                            <th class="px-5 py-3 font-medium">Table</th>
                            <th class="px-5 py-3 font-medium">Items</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Elapsed</th>
                            <th class="px-5 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeOrders as $row)
                            <tr
                                style="border-top:1px solid rgba(255,255,255,0.06);"
                                x-show="filter === 'all' || filter === '{{ $row->status }}'"
                            >
                                <td class="px-5 py-3.5 font-semibold" style="color:#ffffff;">{{ $row->reference }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="rounded-md px-2 py-0.5 text-xs font-semibold" style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.75);">T{{ $row->table_number }}</span>
                                </td>
                                <td class="px-5 py-3.5 admin-muted">{{ $row->items_count }}</td>
                                <td class="px-5 py-3.5">
                                    @if ($row->status === 'ready')
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold" style="border:1px solid rgba(52,211,153,0.45); color:#34d399;">Ready</span>
                                    @elseif ($row->status === 'preparing')
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold" style="background:#0ea5e9; color:#ffffff;">In Progress</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold" style="background:#f97316; color:#ffffff;">Pending</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs admin-muted">{{ $row->elapsed }}</td>
                                <td class="px-5 py-3.5">
                                    @if ($row->status === 'pending' && $row->primary)
                                        <form method="POST" action="{{ route('admin.orders.preparing', $row->primary) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg p-2" style="background:rgba(255,255,255,0.08); color:#38bdf8;" title="Start preparing">▶</button>
                                        </form>
                                    @elseif ($row->status === 'preparing' && $row->primary)
                                        <form method="POST" action="{{ route('admin.orders.served', $row->primary) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg p-2" style="background:rgba(255,255,255,0.08); color:#34d399;" title="Mark ready">✓</button>
                                        </form>
                                    @else
                                        <span class="inline-flex rounded-lg p-2" style="background:rgba(16,185,129,0.12); color:#34d399;">✓</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center text-sm admin-muted">No active orders on the floor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-6 xl:col-span-2">
            <section class="admin-panel p-5">
                <h2 class="font-control text-lg font-semibold" style="color:#ffffff;">Floor Status</h2>
                <div class="mt-4 grid grid-cols-3 gap-2.5 sm:grid-cols-4">
                    @foreach ($floorTables as $table)
                        <div @class([
                            'relative flex aspect-square flex-col items-center justify-center rounded-xl p-2 text-center',
                            'admin-table-empty' => $table->state === 'empty',
                            'admin-table-occupied' => $table->state === 'occupied',
                            'admin-table-attention' => $table->state === 'attention',
                        ])>
                            @if ($table->needs_attention)
                                <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full" style="background:#fb923c; box-shadow:0 0 8px rgba(251,146,60,0.8);"></span>
                            @endif
                            <p class="font-control text-sm font-bold">T{{ $table->table_number }}</p>
                            @if ($table->state === 'empty')
                                <p class="mt-1 text-[9px] font-semibold uppercase tracking-wider" style="opacity:0.7;">Empty</p>
                            @else
                                <p class="mt-1 text-[10px] font-medium" style="opacity:0.85;">{{ $table->guest_count }} guests</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="admin-panel p-5">
                <h2 class="font-control text-lg font-semibold" style="color:#ffffff;">Live Feed</h2>
                <ul class="mt-5 space-y-0">
                    @forelse ($liveFeed as $event)
                        <li class="relative flex gap-3 pb-5 last:pb-0">
                            <div class="flex flex-col items-center">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full" style="background:rgba(14,165,233,0.18); color:#38bdf8;">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
                                </span>
                                @if (! $loop->last)
                                    <span class="mt-1 w-px flex-1" style="background:rgba(255,255,255,0.12);"></span>
                                @endif
                            </div>
                            <div class="min-w-0 pb-1">
                                <p class="text-sm font-medium" style="color:rgba(255,255,255,0.9);">{{ $event->message }}</p>
                                <p class="admin-muted mt-0.5 text-xs">{{ $event->time }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center text-sm admin-muted">Waiting for floor activity…</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</div>
</x-admin-layout>

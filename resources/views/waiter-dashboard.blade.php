<x-waiter-layout title="My Tables" heading="My Tables">
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($tables as $table)
        <article @class([
            'relative flex flex-col rounded-[1.35rem] border bg-white p-4 shadow-[0_1px_3px_rgba(44,33,24,0.06)]',
            'border-[#8b5e3c] ring-1 ring-[#8b5e3c]/20' => $table->needs_attention,
            'border-[#d9cbb8]' => ! $table->needs_attention,
        ])>
            @if ($table->needs_attention)
                <span class="absolute right-4 top-4 h-2.5 w-2.5 rounded-full bg-[#8b5e3c]"></span>
            @endif

            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#8b5e3c] font-display text-sm font-extrabold text-white">
                    T{{ $table->table_number }}
                </div>
                <div class="min-w-0 flex-1 pt-0.5">
                    <div class="flex items-center gap-1.5 text-sm text-ink-soft/65">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ $table->guest_count }}/{{ $table->capacity }}</span>
                    </div>
                    <p class="mt-0.5 text-xs text-ink-soft/45">Seated {{ $table->seated_minutes }}m ago</p>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between gap-2">
                <span class="rounded-full bg-[#f5e6d8] px-2.5 py-1 text-[11px] font-semibold text-[#8b5e3c]">{{ $table->status }}</span>
                <p class="font-display text-lg font-extrabold text-ink">Rs {{ number_format($table->total) }}</p>
            </div>

            <div class="mt-4 flex flex-1 flex-col gap-2">
                <a
                    href="{{ route('waiter.orders.index', ['table' => $table->table_number]) }}"
                    class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-[#3e2723] px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-[#5d4037]"
                >
                    <span class="text-base leading-none">+</span> Add to Order
                </a>
                <div class="grid grid-cols-2 gap-2">
                    <a
                        href="{{ route('waiter.bills.table', $table->table_number) }}"
                        class="rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-center text-xs font-semibold text-ink-soft transition hover:border-[#8b5e3c]"
                    >View Check</a>
                    @if ($table->has_open_service)
                        <form method="POST" action="{{ route('waiter.orders.table.served', $table->table_number) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-xs font-semibold text-ink-soft transition hover:border-[#8b5e3c]">
                                Clear Table
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('waiter.bills.table', $table->table_number) }}"
                            class="rounded-xl border border-[#d9cbb8] bg-white px-3 py-2 text-center text-xs font-semibold text-ink-soft transition hover:border-[#8b5e3c]"
                        >Collect Pay</a>
                    @endif
                </div>
            </div>
        </article>
    @endforeach

    @foreach ($available as $table)
        <article class="flex flex-col items-center justify-between rounded-[1.35rem] border border-dashed border-[#d9cbb8] bg-white/70 p-4">
            <div class="flex w-full items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#efe6da] font-display text-sm font-extrabold text-ink-soft/45">
                    T{{ $table->table_number }}
                </div>
                <p class="pt-2 text-sm text-ink-soft/45">Cap: {{ $table->capacity }}</p>
            </div>

            <div class="flex flex-1 flex-col items-center justify-center py-6 text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[#f0e6da] text-ink-soft/35">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-sm font-semibold text-ink-soft/50">Table Available</p>
            </div>

            <a
                href="{{ route('waiter.orders.index', ['table' => $table->table_number]) }}"
                class="w-full rounded-xl border border-[#d9cbb8] bg-white px-3 py-2.5 text-center text-sm font-semibold text-ink-soft transition hover:border-[#8b5e3c] hover:text-[#8b5e3c]"
            >
                Seat Guests
            </a>
        </article>
    @endforeach
</div>

@if ($tables->isEmpty() && $available->isEmpty())
    <p class="rounded-2xl border border-dashed border-[#d9cbb8] bg-white/60 py-16 text-center text-sm text-ink-soft/55">
        No tables to show yet.
    </p>
@endif
</x-waiter-layout>

@extends('layouts.customer')

@section('title', 'Your Cart')

@section('content')
<div class="mx-auto max-w-2xl space-y-5">
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-[0.15em] text-ink-soft/45">Table {{ $table }}</p>
        <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink">Your cart</h1>
    </div>

    @if ($lines->isEmpty())
        <div class="rounded-2xl border border-dashed border-[#d9cbb8] bg-white/60 px-6 py-14 text-center">
            <p class="text-sm text-ink-soft/60">Your cart is empty.</p>
            <a href="{{ route('customer.menu') }}" class="mt-4 inline-flex rounded-xl bg-[#8b5e3c] px-4 py-2.5 text-sm font-semibold text-white">Browse menu</a>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(44,33,24,0.04)]">
            <ul class="divide-y divide-[#f0e6da]">
                @foreach ($lines as $line)
                    <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div class="min-w-0">
                            <p class="font-semibold text-ink">{{ $line->item->name }}</p>
                            <p class="text-sm text-ink-soft/55">Rs {{ number_format($line->item->price) }} each</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('customer.cart.update', $line->item) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" min="1" max="20" value="{{ $line->quantity }}" class="w-16 rounded-lg border border-[#d9cbb8] px-2 py-1.5 text-center text-sm">
                                <button type="submit" class="rounded-lg border border-[#d9cbb8] px-2.5 py-1.5 text-xs font-semibold text-ink-soft hover:border-[#8b5e3c]">Update</button>
                            </form>
                            <p class="w-24 text-right text-sm font-bold text-[#8b5e3c]">Rs {{ number_format($line->line_total) }}</p>
                            <form method="POST" action="{{ route('customer.cart.remove', $line->item) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-[#f5e6d8] px-2.5 py-1.5 text-xs font-semibold text-[#a0522d]">Remove</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="flex flex-col gap-3 border-t border-[#f0e6da] bg-[#f7f0e8]/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p class="font-display text-xl font-extrabold text-ink">Total · Rs {{ number_format($total) }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('customer.menu') }}" class="rounded-xl border border-[#d9cbb8] bg-white px-4 py-2.5 text-sm font-semibold text-ink-soft">Add more</a>
                    <form method="POST" action="{{ route('customer.order.place') }}">
                        @csrf
                        <button type="submit" class="rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-2.5 text-sm font-semibold text-white shadow-sm">
                            Place order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

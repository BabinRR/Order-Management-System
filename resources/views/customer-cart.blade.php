<x-customer-layout title="Your Cart">
<div class="mx-auto max-w-2xl space-y-6 pt-2">
    <section>
        <h1 class="font-serif-display text-4xl font-semibold tracking-tight text-ink">Your cart.</h1>
        <p class="mt-2 text-sm text-ink-soft/60">Table {{ $table }} · Review and place your order when ready.</p>
    </section>

    @if ($lines->isEmpty())
        <div class="rounded-[1.5rem] border border-dashed border-[#d9cbb8] bg-white/70 px-6 py-16 text-center">
            <p class="font-serif-display text-2xl font-medium text-ink-soft/50">Nothing here yet.</p>
            <p class="mt-2 text-sm text-ink-soft/55">Browse the menu and add something you crave.</p>
            <a href="{{ route('customer.menu') }}" class="mt-6 inline-flex rounded-full bg-[#3e2723] px-5 py-2.5 text-sm font-semibold text-white">Browse menu</a>
        </div>
    @else
        <div class="overflow-hidden rounded-[1.5rem] border border-[#ebe4d8] bg-white shadow-[0_2px_12px_rgba(44,33,24,0.04)]">
            <ul class="divide-y divide-[#f3ebe0]">
                @foreach ($lines as $line)
                    <li class="flex flex-col gap-3 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="font-serif-display text-lg font-semibold text-ink">{{ $line->item->name }}</p>
                            <p class="text-sm text-ink-soft/55">Rs {{ number_format($line->item->price) }} each</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('customer.cart.update', $line->item) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" min="1" max="20" value="{{ $line->quantity }}" class="w-16 rounded-full border border-[#e0d8cc] bg-[#f9f7f2] px-2 py-1.5 text-center text-sm">
                                <button type="submit" class="rounded-full border border-[#e0d8cc] px-3 py-1.5 text-xs font-semibold text-ink-soft hover:border-[#8b5e3c]">Update</button>
                            </form>
                            <p class="w-24 text-right font-serif-display text-lg font-semibold text-ink">Rs {{ number_format($line->line_total) }}</p>
                            <form method="POST" action="{{ route('customer.cart.remove', $line->item) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full bg-[#f5e6d8] px-3 py-1.5 text-xs font-semibold text-[#a0522d]">Remove</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="flex flex-col gap-3 border-t border-[#f3ebe0] bg-[#f9f7f2]/80 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="font-serif-display text-2xl font-semibold text-ink">Total · Rs {{ number_format($total) }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('customer.menu') }}" class="rounded-full border border-[#e0d8cc] bg-white px-4 py-2.5 text-sm font-semibold text-ink-soft">Add more</a>
                    <form method="POST" action="{{ route('customer.order.place') }}">
                        @csrf
                        <button type="submit" class="rounded-full bg-[#3e2723] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#5d4037]">
                            Place order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
</x-customer-layout>

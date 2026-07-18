@extends('layouts.customer')

@section('title', 'Menu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.15em] text-ink-soft/45">Table {{ $table }}</p>
            <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink">Menu</h1>
            <p class="mt-1 text-sm text-ink-soft/60">Tap add — then checkout from your cart.</p>
        </div>
        <a href="{{ route('customer.cart') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#8b5e3c] px-4 py-2.5 text-sm font-semibold text-white shadow-sm">
            View cart
            @if ($cartCount > 0)
                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $cartCount }}</span>
            @endif
        </a>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('customer.menu') }}" @class(['rounded-xl px-3.5 py-2 text-sm font-semibold', 'bg-[#8b5e3c] text-white' => $activeCategory === '', 'border border-[#d9cbb8] bg-white text-ink-soft' => $activeCategory !== ''])>All</a>
        @foreach ($categories as $category)
            <a href="{{ route('customer.menu', ['category' => $category]) }}" @class(['rounded-xl px-3.5 py-2 text-sm font-semibold', 'bg-[#8b5e3c] text-white' => $activeCategory === $category, 'border border-[#d9cbb8] bg-white text-ink-soft' => $activeCategory !== $category])>{{ $category }}</a>
        @endforeach
    </div>

    @forelse ($groupedItems as $category => $items)
        <section class="space-y-3">
            <h2 class="font-display text-xl font-extrabold text-ink">{{ $category }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <article class="flex flex-col overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(44,33,24,0.04)]">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="aspect-[16/10] w-full object-cover">
                        @else
                            <div class="flex aspect-[16/10] items-center justify-center bg-[#f0e6da] text-ink-soft/30">
                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="font-display text-lg font-extrabold text-ink">{{ $item->name }}</h3>
                            <p class="mt-1 flex-1 text-sm leading-relaxed text-ink-soft/65">{{ $item->description }}</p>
                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-[#f0e6da] pt-3">
                                <p class="font-display text-lg font-extrabold text-[#8b5e3c]">Rs {{ number_format($item->price) }}</p>
                                <form method="POST" action="{{ route('customer.cart.add', $item) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-[#8b5e3c] px-3 py-1.5 text-xs font-semibold text-white hover:opacity-95">Add</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <p class="col-span-full rounded-2xl border border-dashed border-[#d9cbb8] bg-white/60 py-16 text-center text-sm text-ink-soft/55">
            No dishes available in this category.
        </p>
    @endforelse
</div>
@endsection

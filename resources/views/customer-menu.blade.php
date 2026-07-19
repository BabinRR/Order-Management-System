<x-customer-layout title="Menu">
@php
    $allCategories = array_keys($groupedItems->toArray());
    $categoryData = [];
    foreach ($groupedItems as $cat => $items) {
        $categoryData[] = [
            'name' => $cat,
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'image_url' => $item->image_url,
            ])->values()->toArray(),
        ];
    }
@endphp

<div
    x-data="{
        categories: {{ Js::from($categoryData) }},
        current: 0,
        prev: null,
        direction: 'forward',
        flipping: false,
        cartCount: {{ $cartCount }},
        flipTo(index) {
            if (index === this.current || this.flipping) return;
            this.direction = index > this.current ? 'forward' : 'backward';
            this.flipping = true;
            this.prev = this.current;
            this.current = index;
            setTimeout(() => { this.flipping = false; this.prev = null; }, 550);
        },
        nextPage() {
            if (this.current < this.categories.length - 1) this.flipTo(this.current + 1);
        },
        prevPage() {
            if (this.current > 0) this.flipTo(this.current - 1);
        },
        get currentCategory() { return this.categories[this.current]; },
        get totalPages() { return this.categories.length; }
    }"
    class="space-y-5"
>
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.15em] text-ink-soft/45">Table {{ $table }}</p>
            <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink">Our Menu</h1>
        </div>
        <a href="{{ route('customer.cart') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#8b5e3c] px-4 py-2.5 text-sm font-semibold text-white shadow-sm">
            View cart
            <template x-if="cartCount > 0">
                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs" x-text="cartCount"></span>
            </template>
        </a>
    </div>

    {{-- The Menu Book --}}
    <div class="menu-book mx-auto w-full max-w-5xl">

        {{-- Book cover shell --}}
        <div class="menu-book-cover rounded-2xl p-1.5 sm:p-2">

            {{-- Gold border line --}}
            <div class="rounded-xl border border-[#c4a574]/30 p-0.5">

                {{-- Inner area: spine + pages --}}
                <div class="flex min-h-[520px] overflow-hidden rounded-lg sm:min-h-[580px]">

                    {{-- Spine --}}
                    <div class="menu-book-spine hidden w-6 shrink-0 items-center justify-center sm:flex">
                        <span class="text-[9px] font-bold uppercase tracking-[0.3em] text-white/30 [writing-mode:vertical-lr]">KitchenDesk</span>
                    </div>

                    {{-- Left panel: Category tabs --}}
                    <div class="flex w-28 shrink-0 flex-col gap-0.5 border-r border-[#d9cbb8]/40 bg-[#f5efe6] p-2 sm:w-36 sm:p-3">
                        <p class="mb-2 px-2 pt-1 text-[9px] font-bold uppercase tracking-[0.2em] text-[#8b5e3c]/50">Categories</p>
                        <template x-for="(cat, idx) in categories" :key="idx">
                            <button
                                @click="flipTo(idx)"
                                :class="current === idx ? 'menu-tab-active' : 'menu-tab-inactive'"
                                class="rounded-lg px-2.5 py-2 text-left text-xs font-semibold transition-all duration-200 sm:text-sm"
                                x-text="cat.name"
                            ></button>
                        </template>

                        {{-- Decorative line --}}
                        <div class="mt-auto px-2">
                            <div class="book-gold-line h-px"></div>
                            <p class="mt-2 text-center text-[8px] font-semibold uppercase tracking-[0.15em] text-ink-soft/30">
                                Page <span x-text="current + 1"></span> of <span x-text="totalPages"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Right panel: Menu page content --}}
                    <div class="relative flex-1 overflow-hidden">
                        {{-- Page stack effect --}}
                        <div class="page-stack absolute inset-0"></div>

                        {{-- Pages --}}
                        <template x-for="(cat, idx) in categories" :key="'page-'+idx">
                            <div
                                x-show="current === idx || prev === idx"
                                x-cloak
                                :class="{
                                    'page-flip-out': prev === idx && direction === 'forward',
                                    'page-flip-in': current === idx && direction === 'forward' && flipping,
                                    'page-flip-out-rev': prev === idx && direction === 'backward',
                                    'page-flip-in-rev': current === idx && direction === 'backward' && flipping,
                                }"
                                class="menu-page absolute inset-0 overflow-y-auto p-4 sm:p-6"
                                style="backface-visibility: hidden;"
                            >
                                {{-- Page header with decorative line --}}
                                <div class="mb-5">
                                    <div class="flex items-center gap-3">
                                        <div class="book-gold-line h-px flex-1"></div>
                                        <h2 class="font-display text-xl font-extrabold text-ink sm:text-2xl" x-text="cat.name"></h2>
                                        <div class="book-gold-line h-px flex-1"></div>
                                    </div>
                                    <p class="mt-1 text-center text-xs text-ink-soft/45">
                                        <span x-text="cat.items.length"></span> dishes
                                    </p>
                                </div>

                                {{-- Menu items grid --}}
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <template x-for="item in cat.items" :key="item.id">
                                        <div class="group flex gap-3 rounded-xl border border-[#e8ddd0] bg-white/60 p-3 transition hover:border-[#c4a574]/50 hover:shadow-sm">
                                            {{-- Food image or placeholder --}}
                                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg sm:h-24 sm:w-24">
                                                <template x-if="item.image_url">
                                                    <img :src="item.image_url" :alt="item.name" class="h-full w-full object-cover">
                                                </template>
                                                <template x-if="!item.image_url">
                                                    <div class="flex h-full w-full items-center justify-center bg-[#f0e6da] text-ink-soft/25">
                                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0-4C6.48 4 2 8.48 2 14s4.48 10 10 10 10-4.48 10-10S17.52 4 12 4z"/></svg>
                                                    </div>
                                                </template>
                                            </div>
                                            {{-- Details --}}
                                            <div class="flex min-w-0 flex-1 flex-col">
                                                <h3 class="font-display text-sm font-extrabold text-ink sm:text-base" x-text="item.name"></h3>
                                                <p class="mt-0.5 flex-1 text-xs leading-relaxed text-ink-soft/55 line-clamp-2" x-text="item.description"></p>
                                                <div class="mt-2 flex items-center justify-between gap-2">
                                                    <p class="font-display text-sm font-extrabold text-[#8b5e3c] sm:text-base">
                                                        Rs <span x-text="Number(item.price).toLocaleString()"></span>
                                                    </p>
                                                    <form method="POST" :action="`{{ url('order/cart') }}/${item.id}`" @submit="cartCount++">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg bg-[#8b5e3c] px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-[#5d4037]">Add</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Page footer --}}
                                <div class="mt-6 flex items-center justify-center gap-3">
                                    <div class="book-gold-line h-px w-12"></div>
                                    <span class="text-[10px] font-semibold text-[#c4a574]">&#10087;</span>
                                    <div class="book-gold-line h-px w-12"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Page turn controls --}}
        <div class="mt-4 flex items-center justify-center gap-4">
            <button
                @click="prevPage()"
                :disabled="current === 0"
                :class="current === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-[#8b5e3c] hover:text-white'"
                class="flex h-10 w-10 items-center justify-center rounded-full border border-[#d9cbb8] bg-white text-ink-soft transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <div class="flex items-center gap-1.5">
                <template x-for="(cat, idx) in categories" :key="'dot-'+idx">
                    <button
                        @click="flipTo(idx)"
                        :class="current === idx ? 'bg-[#8b5e3c] w-6' : 'bg-[#d9cbb8] w-2 hover:bg-[#a67c52]'"
                        class="h-2 rounded-full transition-all duration-300"
                    ></button>
                </template>
            </div>

            <button
                @click="nextPage()"
                :disabled="current === totalPages - 1"
                :class="current === totalPages - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-[#8b5e3c] hover:text-white'"
                class="flex h-10 w-10 items-center justify-center rounded-full border border-[#d9cbb8] bg-white text-ink-soft transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>

@if ($groupedItems->isEmpty())
    <p class="rounded-2xl border border-dashed border-[#d9cbb8] bg-white/60 py-16 text-center text-sm text-ink-soft/55">
        No dishes available right now.
    </p>
@endif
</x-customer-layout>

@php
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning.' : ($hour < 17 ? 'Good afternoon.' : 'Good evening.');

    $gradients = [
        'Starters' => 'from-[#c4784a] to-[#8b5e3c]',
        'Mains' => 'from-[#a0522d] to-[#5d4037]',
        'Desserts' => 'from-[#b8956c] to-[#8b5e3c]',
        'Drinks' => 'from-[#6b8f71] to-[#3e5c42]',
        'Sides' => 'from-[#a67c52] to-[#5d4037]',
    ];

    $flatItems = $groupedItems->flatten(1);
@endphp

<x-customer-layout title="Menu">
<div class="space-y-8">
    {{-- Hero greeting --}}
    <section class="max-w-2xl pt-2">
        <h1 class="font-serif-display text-4xl font-semibold leading-[1.1] tracking-tight text-ink sm:text-5xl">
            {{ $greeting }}
        </h1>
        <p class="font-serif-display mt-2 text-3xl font-medium leading-tight text-ink-soft/55 sm:text-4xl">
            What are you craving?
        </p>
        <p class="mt-4 max-w-lg text-sm leading-relaxed text-ink-soft/60">
            Browse our seasonal menu and add items directly to your table’s order. The kitchen will start preparing once you place your order.
        </p>
    </section>

    {{-- Category pills --}}
    <div class="flex flex-wrap gap-2">
        <a
            href="{{ route('customer.menu') }}"
            @class([
                'rounded-full px-4 py-2 text-sm font-semibold transition',
                'bg-[#3e2723] text-white shadow-sm' => $activeCategory === '',
                'border border-[#e0d8cc] bg-white text-ink-soft hover:border-[#8b5e3c]' => $activeCategory !== '',
            ])
        >All</a>
        @foreach ($categories as $category)
            <a
                href="{{ route('customer.menu', ['category' => $category]) }}"
                @class([
                    'rounded-full px-4 py-2 text-sm font-semibold transition',
                    'bg-[#3e2723] text-white shadow-sm' => $activeCategory === $category,
                    'border border-[#e0d8cc] bg-white text-ink-soft hover:border-[#8b5e3c]' => $activeCategory !== $category,
                ])
            >{{ $category }}</a>
        @endforeach
    </div>

    {{-- Menu grid --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($flatItems as $item)
            @php
                $gradient = $gradients[$item->category] ?? 'from-[#a67c52] to-[#5d4037]';
            @endphp
            <article class="flex flex-col overflow-hidden rounded-[1.5rem] border border-[#ebe4d8] bg-white shadow-[0_2px_12px_rgba(44,33,24,0.04)]">
                <div class="relative aspect-[5/3.2] overflow-hidden">
                    @if ($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/25 to-transparent"></div>
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br {{ $gradient }}">
                            <svg class="h-12 w-12 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0-5v2m0 14v2m9-9h-2M5 12H3"/>
                            </svg>
                        </div>
                    @endif
                    <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-[#5d4037] backdrop-blur-sm">
                        {{ $item->category }}
                    </span>
                </div>

                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-serif-display text-xl font-semibold leading-snug text-ink">{{ $item->name }}</h2>
                        <p class="font-serif-display shrink-0 text-xl font-semibold text-ink">Rs {{ number_format($item->price) }}</p>
                    </div>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-ink-soft/60">
                        {{ $item->description ?: 'A carefully prepared dish from our kitchen.' }}
                    </p>
                    <form method="POST" action="{{ route('customer.cart.add', $item) }}" class="mt-5">
                        @csrf
                        <button
                            type="submit"
                            class="w-full rounded-full bg-[#efe8de] px-4 py-3 text-sm font-semibold text-ink transition hover:bg-[#8b5e3c] hover:text-white"
                        >
                            Add to Order
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-[1.5rem] border border-dashed border-[#d9cbb8] bg-white/70 py-16 text-center text-sm text-ink-soft/55">
                No dishes available in this category yet.
            </p>
        @endforelse
    </div>
</div>
</x-customer-layout>

@extends('layouts.admin')

@section('title', 'Food Menu')
@section('eyebrow', 'Catalog')
@section('heading', 'Food Menu')

@section('content')
@php
    $formErrors = $errors->any();
@endphp
<div
    x-data="{
        search: '',
        categoryFilter: '',
        modalOpen: {{ $formErrors ? 'true' : 'false' }},
        editingId: @js(old('menu_id')),
        deleteTarget: null,
        storeUrl: '{{ route('admin.menu.store') }}',
        baseUrl: '{{ url('admin/menu') }}',
        form: {
            name: @js(old('name', '')),
            category: @js(old('category', $categories[0])),
            price: @js((int) old('price', 0)),
            description: @js(old('description', '')),
            status: @js(old('status', $statuses[0])),
        },
        openCreate() {
            this.editingId = null;
            this.form = { name: '', category: @js($categories[0]), price: 0, description: '', status: @js($statuses[0]) };
            this.modalOpen = true;
        },
        openEdit(item) {
            this.editingId = item.id;
            this.form = { name: item.name, category: item.category, price: item.price, description: item.description ?? '', status: item.status };
            this.modalOpen = true;
        },
        matches(name, category, description, itemCategory) {
            const q = this.search.toLowerCase().trim();
            const matchSearch = !q || (name + ' ' + description).toLowerCase().includes(q);
            const matchCat = !this.categoryFilter || itemCategory === this.categoryFilter;
            return matchSearch && matchCat;
        }
    }"
    class="space-y-5"
>
    {{-- Toolbar --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-[#d8e0da] bg-white p-4 shadow-[0_1px_2px_rgba(26,31,28,0.04)] lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative sm:min-w-[260px]">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-soft/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Search dishes…"
                    class="w-full rounded-xl border border-[#d8e0da] bg-[#f6f8f6] py-2.5 pl-10 pr-4 text-sm outline-none ring-[#3d6b4f]/25 transition placeholder:text-ink-soft/40 focus:border-[#3d6b4f] focus:bg-white focus:ring-2"
                >
            </div>
            <select
                x-model="categoryFilter"
                class="rounded-xl border border-[#d8e0da] bg-[#f6f8f6] px-3 py-2.5 text-sm font-medium outline-none transition focus:border-[#3d6b4f] focus:bg-white focus:ring-2 focus:ring-[#3d6b4f]/25"
            >
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <button
            type="button"
            @click="openCreate()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-[#3d6b4f] to-[#2a4a37] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add food item
        </button>
    </div>

    {{-- Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($items as $item)
            <article
                class="flex flex-col rounded-2xl border border-[#d8e0da] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)] transition hover:border-[#3d6b4f]/40 hover:shadow-md"
                x-show="matches(@js($item->name), @js($item->category), @js($item->description ?? ''), @js($item->category))"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <span class="inline-flex rounded-lg bg-[#eef2ef] px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-ink-soft/65">{{ $item->category }}</span>
                        <h3 class="mt-2 font-display text-lg font-extrabold text-ink">{{ $item->name }}</h3>
                    </div>
                    @php
                        $statusClass = match ($item->status) {
                            'Available' => 'bg-[#e4efe8] text-[#2a4a37]',
                            'Sold Out' => 'bg-[#fdf0e8] text-[#c45c26]',
                            default => 'bg-[#eef2ef] text-ink-soft',
                        };
                    @endphp
                    <span class="shrink-0 rounded-lg px-2 py-1 text-[11px] font-semibold {{ $statusClass }}">{{ $item->status }}</span>
                </div>
                <p class="mt-2 flex-1 text-sm leading-relaxed text-ink-soft/65">{{ $item->description }}</p>
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-[#eef2ef] pt-4">
                    <p class="font-display text-xl font-extrabold text-[#3d6b4f]">Rs {{ number_format($item->price) }}</p>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="openEdit(@js([
                                'id' => $item->id,
                                'name' => $item->name,
                                'category' => $item->category,
                                'price' => $item->price,
                                'description' => $item->description,
                                'status' => $item->status,
                            ]))"
                            class="rounded-lg border border-[#d8e0da] bg-white px-3 py-1.5 text-xs font-semibold text-ink transition hover:border-[#3d6b4f] hover:text-[#3d6b4f]"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            @click="deleteTarget = @js(['id' => $item->id, 'name' => $item->name])"
                            class="rounded-lg bg-[#fdf0e8] px-3 py-1.5 text-xs font-semibold text-[#c45c26] transition hover:bg-[#f5e0d2]"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-[#d8e0da] bg-white/50 py-16 text-center text-sm text-ink-soft/55">
                No food items yet. Add your first dish.
            </p>
        @endforelse
    </div>

    {{-- Form modal --}}
    <div
        x-show="modalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end justify-center bg-ink/45 p-4 backdrop-blur-sm sm:items-center"
        @keydown.escape.window="modalOpen = false"
    >
        <div @click.outside="modalOpen = false" class="modal-enter w-full max-w-lg rounded-2xl border border-[#d8e0da] bg-white p-5 shadow-2xl sm:p-6">
            <div class="mb-5 flex items-start justify-between">
                <div>
                    <h2 class="font-display text-xl font-extrabold text-ink" x-text="editingId ? 'Edit food item' : 'Add food item'"></h2>
                    <p class="mt-0.5 text-sm text-ink-soft/60">Set pricing, category, and availability.</p>
                </div>
                <button type="button" @click="modalOpen = false" class="rounded-lg p-1.5 text-ink-soft/50 transition hover:bg-[#eef2ef] hover:text-ink">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="editingId ? baseUrl + '/' + editingId : storeUrl" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editingId ? 'PUT' : 'POST'">
                <input type="hidden" name="menu_id" :value="editingId">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Name</label>
                    <input x-model="form.name" name="name" type="text" @class(['w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#3d6b4f]/25', 'border-[#c45c26]' => $errors->has('name'), 'border-[#d8e0da] focus:border-[#3d6b4f]' => ! $errors->has('name')])>
                    @error('name') <p class="mt-1 text-xs font-medium text-[#c45c26]">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Category</label>
                        <select x-model="form.category" name="category" class="w-full rounded-xl border border-[#d8e0da] px-3 py-2.5 text-sm outline-none focus:border-[#3d6b4f] focus:ring-2 focus:ring-[#3d6b4f]/25">
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Price (Rs)</label>
                        <input x-model.number="form.price" name="price" type="number" min="0" step="1" @class(['w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#3d6b4f]/25', 'border-[#c45c26]' => $errors->has('price'), 'border-[#d8e0da] focus:border-[#3d6b4f]' => ! $errors->has('price')])>
                        @error('price') <p class="mt-1 text-xs font-medium text-[#c45c26]">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Description</label>
                    <textarea x-model="form.description" name="description" rows="3" class="w-full rounded-xl border border-[#d8e0da] px-3 py-2.5 text-sm outline-none focus:border-[#3d6b4f] focus:ring-2 focus:ring-[#3d6b4f]/25"></textarea>
                    @error('description') <p class="mt-1 text-xs font-medium text-[#c45c26]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Status</label>
                    <select x-model="form.status" name="status" class="w-full rounded-xl border border-[#d8e0da] px-3 py-2.5 text-sm outline-none focus:border-[#3d6b4f] focus:ring-2 focus:ring-[#3d6b4f]/25">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="rounded-xl border border-[#d8e0da] px-4 py-2.5 text-sm font-semibold text-ink-soft transition hover:bg-[#eef2ef]">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-br from-[#3d6b4f] to-[#2a4a37] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95" x-text="editingId ? 'Save changes' : 'Add item'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete confirm --}}
    <div
        x-show="deleteTarget"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end justify-center bg-ink/45 p-4 backdrop-blur-sm sm:items-center"
    >
        <div @click.outside="deleteTarget = null" class="modal-enter w-full max-w-md rounded-2xl border border-[#d8e0da] bg-white p-6 shadow-2xl">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fdf0e8] text-[#c45c26]">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h2 class="font-display text-xl font-extrabold text-ink">Delete food item?</h2>
            <p class="mt-2 text-sm text-ink-soft/70">
                Remove <span class="font-semibold text-ink" x-text="deleteTarget?.name"></span> from the menu? This can’t be undone.
            </p>
            <form :action="baseUrl + '/' + deleteTarget?.id" method="POST" class="mt-5 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteTarget = null" class="rounded-xl border border-[#d8e0da] px-4 py-2.5 text-sm font-semibold text-ink-soft transition hover:bg-[#eef2ef]">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#c45c26] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#a34b1f]">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

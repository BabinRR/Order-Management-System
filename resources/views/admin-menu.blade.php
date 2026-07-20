<x-admin-layout title="Food Menu">
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
    <div class="flex flex-col gap-3 admin-card rounded-2xl p-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative sm:min-w-[260px]">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Search dishes…"
                    class="w-full rounded-xl border border-white/10 bg-[#0f0f0f] py-2.5 pl-10 pr-4 text-sm outline-none ring-rose-500/25 transition placeholder:text-white/30 focus:border-rose-500 focus:bg-[#141414] focus:ring-2 text-white"
                >
            </div>
            <select
                x-model="categoryFilter"
                class="rounded-xl border border-white/10 bg-[#0f0f0f] px-3 py-2.5 text-sm font-medium outline-none transition focus:border-rose-500 focus:bg-[#141414] focus:ring-2 focus:ring-rose-500/25 text-white"
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
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add food item
        </button>
    </div>

    {{-- Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($items as $item)
            <article
                class="flex flex-col overflow-hidden admin-card rounded-2xl transition hover:border-rose-500/40 hover:shadow-md"
                x-show="matches(@js($item->name), @js($item->category), @js($item->description ?? ''), @js($item->category))"
            >
                @if ($item->image_url)
                    <div class="aspect-[16/10] overflow-hidden bg-white/10">
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                    </div>
                @else
                    <div class="flex aspect-[16/10] items-center justify-center bg-white/10 text-white/60/35">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
                <div class="flex flex-1 flex-col p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <span class="inline-flex rounded-lg bg-white/10 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-white/55">{{ $item->category }}</span>
                        <h3 class="mt-2 font-control text-lg font-extrabold text-white">{{ $item->name }}</h3>
                    </div>
                    @php
                        $statusClass = match ($item->status) {
                            'Available' => 'bg-emerald-500/15 text-emerald-400',
                            'Sold Out' => 'bg-rose-500/15 text-rose-400',
                            default => 'bg-white/10 text-white/60',
                        };
                    @endphp
                    <span class="shrink-0 rounded-lg px-2 py-1 text-[11px] font-semibold {{ $statusClass }}">{{ $item->status }}</span>
                </div>
                <p class="mt-2 flex-1 text-sm leading-relaxed text-white/55">{{ $item->description }}</p>
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-white/8 pt-4">
                    <p class="font-control text-xl font-extrabold text-rose-400">Rs {{ number_format($item->price) }}</p>
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
                            class="rounded-lg admin-card px-3 py-1.5 text-xs font-semibold text-white transition hover:border-rose-500 hover:text-rose-400"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            @click="deleteTarget = @js(['id' => $item->id, 'name' => $item->name])"
                            class="rounded-lg bg-rose-500/15 px-3 py-1.5 text-xs font-semibold text-rose-400 transition hover:bg-rose-500/25"
                        >
                            Delete
                        </button>
                    </div>
                </div>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-white/15 bg-white/50 py-16 text-center text-sm text-white/45">
                No food items yet. Add your first dish.
            </p>
        @endforelse
    </div>

    {{-- Form modal --}}
    <div
        x-show="modalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/70 p-4 backdrop-blur-sm sm:items-center"
        @keydown.escape.window="modalOpen = false"
    >
        <div @click.outside="modalOpen = false" class="modal-enter w-full max-w-lg admin-card rounded-2xl p-5 shadow-2xl text-white sm:p-6">
            <div class="mb-5 flex items-start justify-between">
                <div>
                    <h2 class="font-control text-xl font-extrabold text-white" x-text="editingId ? 'Edit food item' : 'Add food item'"></h2>
                    <p class="mt-0.5 text-sm text-white/50">Set pricing, category, and availability.</p>
                </div>
                <button type="button" @click="modalOpen = false" class="rounded-lg p-1.5 text-white/40 transition hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="editingId ? baseUrl + '/' + editingId : storeUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editingId ? 'PUT' : 'POST'">
                <input type="hidden" name="menu_id" :value="editingId">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Photo</label>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-xl border border-white/10 bg-[#0f0f0f] px-3 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-rose-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
                    @error('image') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Name</label>
                    <input x-model="form.name" name="name" type="text" @class(['w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25', 'border-rose-500' => $errors->has('name'), 'border-white/15 focus:border-rose-500' => ! $errors->has('name')])>
                    @error('name') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Category</label>
                        <select x-model="form.category" name="category" class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25">
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Price (Rs)</label>
                        <input x-model.number="form.price" name="price" type="number" min="0" step="1" @class(['w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25', 'border-rose-500' => $errors->has('price'), 'border-white/15 focus:border-rose-500' => ! $errors->has('price')])>
                        @error('price') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Description</label>
                    <textarea x-model="form.description" name="description" rows="3" class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25"></textarea>
                    @error('description') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Status</label>
                    <select x-model="form.status" name="status" class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-white/60 transition hover:bg-white/10">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95" x-text="editingId ? 'Save changes' : 'Add item'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete confirm --}}
    <div
        x-show="deleteTarget"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/70 p-4 backdrop-blur-sm sm:items-center"
    >
        <div @click.outside="deleteTarget = null" class="modal-enter w-full max-w-md admin-card rounded-2xl p-6 shadow-2xl text-white">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/15 text-rose-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h2 class="font-control text-xl font-extrabold text-white">Delete food item?</h2>
            <p class="mt-2 text-sm text-white/60">
                Remove <span class="font-semibold text-white" x-text="deleteTarget?.name"></span> from the menu? This can’t be undone.
            </p>
            <form :action="baseUrl + '/' + deleteTarget?.id" method="POST" class="mt-5 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteTarget = null" class="rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-white/60 transition hover:bg-white/10">Cancel</button>
                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">Delete</button>
            </form>
        </div>
    </div>
</div>
</x-admin-layout>

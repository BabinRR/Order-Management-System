@extends('layouts.admin')

@section('title', 'Workers')
@section('eyebrow', 'Team')
@section('heading', 'Workers')

@section('content')
@php
    $formErrors = $errors->any();
@endphp
<div
    x-data="{
        search: '',
        modalOpen: {{ $formErrors ? 'true' : 'false' }},
        editingId: @js(old('worker_id')),
        deleteTarget: null,
        storeUrl: '{{ route('admin.workers.store') }}',
        baseUrl: '{{ url('admin/workers') }}',
        form: {
            name: @js(old('name', '')),
            role: @js(old('role', $roles[0])),
            email: @js(old('email', '')),
            phone: @js(old('phone', '')),
            shift: @js(old('shift', $shifts[0])),
            status: @js(old('status', $statuses[0])),
        },
        openCreate() {
            this.editingId = null;
            this.form = { name: '', role: @js($roles[0]), email: '', phone: '', shift: @js($shifts[0]), status: @js($statuses[0]) };
            this.modalOpen = true;
        },
        openEdit(worker) {
            this.editingId = worker.id;
            this.form = { name: worker.name, role: worker.role, email: worker.email, phone: worker.phone ?? '', shift: worker.shift, status: worker.status };
            this.modalOpen = true;
        },
        matches(row) {
            const q = this.search.toLowerCase().trim();
            return !q || row.toLowerCase().includes(q);
        }
    }"
    class="space-y-5"
>
    {{-- Toolbar --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-[#d9cbb8] bg-white p-4 shadow-[0_1px_2px_rgba(26,31,28,0.04)] sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="relative max-w-md flex-1 sm:min-w-[280px]">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-soft/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Search by name, role, or email…"
                    class="w-full rounded-xl border border-[#d9cbb8] bg-[#f7f0e8] py-2.5 pl-10 pr-4 text-sm outline-none ring-[#8b5e3c]/25 transition placeholder:text-ink-soft/40 focus:border-[#8b5e3c] focus:bg-white focus:ring-2"
                >
            </div>
            <span class="hidden rounded-lg bg-[#f0e6da] px-2.5 py-1 text-xs font-semibold text-ink-soft/60 sm:inline-flex">{{ $workers->count() }} total</span>
        </div>
        <button
            type="button"
            @click="openCreate()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add worker
        </button>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-[#f7f0e8] text-xs uppercase tracking-wider text-ink-soft/50">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Worker</th>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Role</th>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Shift</th>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Status</th>
                        <th class="px-5 py-3.5 text-right font-semibold sm:px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0e6da]">
                    @forelse ($workers as $worker)
                        <tr
                            class="transition hover:bg-[#f7f0e8]/70"
                            x-show="matches(@js($worker->name.' '.$worker->role.' '.$worker->email.' '.$worker->shift.' '.$worker->status))"
                        >
                            <td class="px-5 py-3.5 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#ede0d0] to-[#d0e2d7] text-xs font-bold text-[#5d4037]">
                                        {{ $worker->initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-ink">{{ $worker->name }}</p>
                                        <p class="truncate text-xs text-ink-soft/55">{{ $worker->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-ink-soft sm:px-6">{{ $worker->role }}</td>
                            <td class="px-5 py-3.5 sm:px-6">
                                <span class="inline-flex rounded-md bg-[#f0e6da] px-2 py-0.5 text-xs font-medium text-ink-soft/70">{{ $worker->shift }}</span>
                            </td>
                            <td class="px-5 py-3.5 sm:px-6">
                                @php
                                    $statusClass = match ($worker->status) {
                                        'Active' => 'bg-[#ede0d0] text-[#5d4037]',
                                        'On Leave' => 'bg-[#f5e6d8] text-[#a0522d]',
                                        default => 'bg-[#f0e6da] text-ink-soft',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                    {{ $worker->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 sm:px-6">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        @click="openEdit(@js([
                                            'id' => $worker->id,
                                            'name' => $worker->name,
                                            'role' => $worker->role,
                                            'email' => $worker->email,
                                            'phone' => $worker->phone,
                                            'shift' => $worker->shift,
                                            'status' => $worker->status,
                                        ]))"
                                        class="rounded-lg border border-[#d9cbb8] bg-white px-3 py-1.5 text-xs font-semibold text-ink transition hover:border-[#8b5e3c] hover:text-[#8b5e3c]"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteTarget = @js(['id' => $worker->id, 'name' => $worker->name])"
                                        class="rounded-lg bg-[#f5e6d8] px-3 py-1.5 text-xs font-semibold text-[#a0522d] transition hover:bg-[#edd5c4]"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-ink-soft/55">No workers yet. Add your first team member.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form modal --}}
    <div
        x-show="modalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end justify-center bg-ink/45 p-4 backdrop-blur-sm sm:items-center"
        @keydown.escape.window="modalOpen = false"
    >
        <div @click.outside="modalOpen = false" class="modal-enter w-full max-w-lg rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-2xl sm:p-6">
            <div class="mb-5 flex items-start justify-between">
                <div>
                    <h2 class="font-display text-xl font-extrabold text-ink" x-text="editingId ? 'Edit worker' : 'Add worker'"></h2>
                    <p class="mt-0.5 text-sm text-ink-soft/60">Manage staff details and shift assignment.</p>
                </div>
                <button type="button" @click="modalOpen = false" class="rounded-lg p-1.5 text-ink-soft/50 transition hover:bg-[#f0e6da] hover:text-ink">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="editingId ? baseUrl + '/' + editingId : storeUrl" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editingId ? 'PUT' : 'POST'">
                <input type="hidden" name="worker_id" :value="editingId">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Full name</label>
                    <input x-model="form.name" name="name" type="text" @class(['w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25', 'border-[#a0522d]' => $errors->has('name'), 'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('name')])>
                    @error('name') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Role</label>
                        <select x-model="form.role" name="role" class="w-full rounded-xl border border-[#d9cbb8] px-3 py-2.5 text-sm outline-none focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/25">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Shift</label>
                        <select x-model="form.shift" name="shift" class="w-full rounded-xl border border-[#d9cbb8] px-3 py-2.5 text-sm outline-none focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/25">
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift }}">{{ $shift }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Email</label>
                    <input x-model="form.email" name="email" type="email" @class(['w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25', 'border-[#a0522d]' => $errors->has('email'), 'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('email')])>
                    @error('email') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Phone</label>
                        <input x-model="form.phone" name="phone" type="text" class="w-full rounded-xl border border-[#d9cbb8] px-3 py-2.5 text-sm outline-none focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/25">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Status</label>
                        <select x-model="form.status" name="status" class="w-full rounded-xl border border-[#d9cbb8] px-3 py-2.5 text-sm outline-none focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/25">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="rounded-xl border border-[#d9cbb8] px-4 py-2.5 text-sm font-semibold text-ink-soft transition hover:bg-[#f0e6da]">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95" x-text="editingId ? 'Save changes' : 'Add worker'"></button>
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
        <div @click.outside="deleteTarget = null" class="modal-enter w-full max-w-md rounded-2xl border border-[#d9cbb8] bg-white p-6 shadow-2xl">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f5e6d8] text-[#a0522d]">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h2 class="font-display text-xl font-extrabold text-ink">Delete worker?</h2>
            <p class="mt-2 text-sm text-ink-soft/70">
                Remove <span class="font-semibold text-ink" x-text="deleteTarget?.name"></span> from the staff list? This can’t be undone.
            </p>
            <form :action="baseUrl + '/' + deleteTarget?.id" method="POST" class="mt-5 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteTarget = null" class="rounded-xl border border-[#d9cbb8] px-4 py-2.5 text-sm font-semibold text-ink-soft transition hover:bg-[#f0e6da]">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#a0522d] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#804020]">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

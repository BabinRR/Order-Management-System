<x-admin-layout title="Workers">
@php
    $formErrors = $errors->any();
@endphp
<div
    x-data="{
        search: '',
        modalOpen: {{ $formErrors ? 'true' : 'false' }},
        editingId: @js(old('worker_id')),
        deleteTarget: null,
        deleteUrl: '',
        storeUrl: '{{ route('admin.workers.store') }}',
        baseUrl: '{{ url('admin/workers') }}',
        form: {
            name: @js(old('name', '')),
            role: @js(old('role', $roles[0])),
            email: @js(old('email', '')),
            phone: @js(old('phone', '')),
            shift: @js(old('shift', $shifts[0])),
            status: @js(old('status', $statuses[0])),
            salary: @js((int) old('salary', 20000)),
            password: '',
            password_confirmation: '',
        },
        salaryTarget: null,
        salaryAction: 'increase',
        salaryAmount: 1000,
        get needsLogin() {
            return ['Waiter', 'Waitress'].includes(this.form.role);
        },
        openCreate() {
            this.editingId = null;
            this.form = { name: '', role: @js($roles[0]), email: '', phone: '', shift: @js($shifts[0]), status: @js($statuses[0]), salary: 20000, password: '', password_confirmation: '' };
            this.modalOpen = true;
        },
        openEdit(worker) {
            this.editingId = worker.id;
            this.form = { name: worker.name, role: worker.role, email: worker.email, phone: worker.phone ?? '', shift: worker.shift, status: worker.status, salary: worker.salary ?? 0, password: '', password_confirmation: '' };
            this.modalOpen = true;
        },
        openSalary(worker) {
            this.salaryTarget = worker;
            this.salaryAction = 'increase';
            this.salaryAmount = 1000;
        },
        closeSalary() {
            this.salaryTarget = null;
        },
        openDelete(worker) {
            this.deleteTarget = worker;
            this.deleteUrl = this.baseUrl + '/' + worker.id + '/delete';
        },
        closeDelete() {
            this.deleteTarget = null;
            this.deleteUrl = '';
        },
        matches(row) {
            const q = this.search.toLowerCase().trim();
            return !q || row.toLowerCase().includes(q);
        }
    }"
    class="space-y-5"
>
    @if (session('invite_code'))
        <div class="rounded-2xl border border-amber-400/40 bg-amber-500/10 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-300">Waiter invite details</p>
            <p class="mt-2 text-sm text-white/80">Default password: <span class="font-mono font-bold text-white">1234abcd</span></p>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-amber-300">Gmail change-password code</p>
            <p class="font-control mt-1 text-3xl font-bold tracking-[0.2em] text-white">{{ session('invite_code') }}</p>
            <p class="mt-2 text-sm text-white/65">{{ session('status') }}</p>
            <p class="mt-1 text-xs text-white/45">Waiter signs in with their <strong class="text-white/70">name</strong> + default password, then enters this Gmail code to set a new password.</p>
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-col gap-3 admin-card rounded-2xl p-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="relative max-w-md flex-1 sm:min-w-[280px]">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Search by name, role, or email…"
                    class="w-full rounded-xl border border-white/10 bg-[#0f0f0f] py-2.5 pl-10 pr-4 text-sm outline-none ring-rose-500/25 transition placeholder:text-white/30 focus:border-rose-500 focus:bg-[#141414] focus:ring-2 text-white"
                >
            </div>
            <span class="hidden rounded-lg bg-white/10 px-2.5 py-1 text-xs font-semibold text-white/50 sm:inline-flex">{{ $workers->count() }} total</span>
        </div>
        <button
            type="button"
            @click="openCreate()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add worker
        </button>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden admin-card rounded-2xl shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-[#0f0f0f] text-xs uppercase tracking-wider text-white/40">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Worker</th>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Role</th>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Shift</th>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Salary</th>
                        <th class="px-5 py-3.5 font-semibold sm:px-6">Status</th>
                        <th class="px-5 py-3.5 text-right font-semibold sm:px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/8">
                    @forelse ($workers as $worker)
                        <tr
                            class="transition hover:bg-[#0f0f0f]/70"
                            x-show="matches(@js($worker->name.' '.$worker->role.' '.$worker->email.' '.$worker->shift.' '.$worker->status))"
                        >
                            <td class="px-5 py-3.5 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-white/10 to-white/5 text-xs font-bold text-emerald-400">
                                        {{ $worker->initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-white">{{ $worker->name }}</p>
                                        <p class="truncate text-xs text-white/45">{{ $worker->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-white/60 sm:px-6">{{ $worker->role }}</td>
                            <td class="px-5 py-3.5 sm:px-6">
                                <span class="inline-flex rounded-md bg-white/10 px-2 py-0.5 text-xs font-medium text-white/60">{{ $worker->shift }}</span>
                            </td>
                            <td class="px-5 py-3.5 sm:px-6">
                                <p class="font-semibold text-white">Rs {{ number_format($worker->salary) }}</p>
                                <button
                                    type="button"
                                    @click="openSalary(@js([
                                        'id' => $worker->id,
                                        'name' => $worker->name,
                                        'salary' => $worker->salary,
                                    ]))"
                                    class="mt-1 text-[11px] font-semibold text-sky-300 hover:underline"
                                >
                                    Adjust
                                </button>
                            </td>
                            <td class="px-5 py-3.5 sm:px-6">
                                @php
                                    $statusClass = match ($worker->status) {
                                        'Active' => 'bg-emerald-500/15 text-emerald-400',
                                        'On Leave' => 'bg-rose-500/15 text-rose-400',
                                        default => 'bg-white/10 text-white/60',
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
                                            'salary' => $worker->salary,
                                        ]))"
                                        class="rounded-lg admin-card px-3 py-1.5 text-xs font-semibold text-white transition hover:border-rose-500 hover:text-rose-400"
                                    >
                                        Edit
                                    </button>
                                    @if ($worker->isLoginRole() && $worker->user_id)
                                        <form method="POST" action="{{ route('admin.workers.resend-invite', $worker) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-sky-500/30 bg-sky-500/10 px-3 py-1.5 text-xs font-semibold text-sky-300 transition hover:bg-sky-500/20">
                                                Resend code
                                            </button>
                                        </form>
                                    @endif
                                    <button
                                        type="button"
                                        @click="openDelete(@js(['id' => $worker->id, 'name' => $worker->name]))"
                                        class="rounded-lg bg-rose-500/15 px-3 py-1.5 text-xs font-semibold text-rose-400 transition hover:bg-rose-500/25"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-sm text-white/45">No workers yet. Add your first team member.</td>
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
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/70 p-4 backdrop-blur-sm sm:items-center"
        @keydown.escape.window="modalOpen = false"
    >
        <div @click.outside="modalOpen = false" class="modal-enter w-full max-w-lg admin-card rounded-2xl p-5 shadow-2xl text-white sm:p-6">
            <div class="mb-5 flex items-start justify-between">
                <div>
                    <h2 class="font-control text-xl font-extrabold text-white" x-text="editingId ? 'Edit worker' : 'Add worker'"></h2>
                    <p class="mt-0.5 text-sm text-white/50">Manage staff details and shift assignment.</p>
                </div>
                <button type="button" @click="modalOpen = false" class="rounded-lg p-1.5 text-white/40 transition hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="editingId ? baseUrl + '/' + editingId : storeUrl" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editingId ? 'PUT' : 'POST'">
                <input type="hidden" name="worker_id" :value="editingId">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Full name</label>
                    <input x-model="form.name" name="name" type="text" @class(['w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25', 'border-rose-500' => $errors->has('name'), 'border-white/15 focus:border-rose-500' => ! $errors->has('name')])>
                    @error('name') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Role</label>
                        <select x-model="form.role" name="role" class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Shift</label>
                        <select x-model="form.shift" name="shift" class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25">
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift }}">{{ $shift }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Email</label>
                    <input x-model="form.email" name="email" type="email" @class(['w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25', 'border-rose-500' => $errors->has('email'), 'border-white/15 focus:border-rose-500' => ! $errors->has('email')])>
                    @error('email') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Phone</label>
                        <input x-model="form.phone" name="phone" type="text" class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Status</label>
                        <select x-model="form.status" name="status" class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Monthly salary (Rs)</label>
                    <input x-model="form.salary" name="salary" type="number" min="0" step="100" @class(['w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25', 'border-rose-500' => $errors->has('salary'), 'border-white/15 focus:border-rose-500' => ! $errors->has('salary')])>
                    @error('salary') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <template x-if="needsLogin">
                    <div class="space-y-2 rounded-xl border border-white/10 bg-[#0f0f0f]/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-rose-400">Waiter login account</p>
                        <p class="text-xs leading-relaxed text-white/55">
                            Creates a login with default password <span class="font-mono text-white/80">1234abcd</span>.
                            A verification code is emailed to their Gmail — required when they change password after first login.
                            They sign in with their <span class="text-white/80">name</span>.
                        </p>
                        <p class="text-xs text-white/40" x-show="editingId">
                            Already has an account? Use <span class="text-white/70">Resend code</span> to reset to the default password and send a new Gmail code.
                        </p>
                    </div>
                </template>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-white/60 transition hover:bg-white/10">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95" x-text="editingId ? 'Save changes' : 'Add worker'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Salary adjust --}}
    <div
        x-show="salaryTarget"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/70 p-4 backdrop-blur-sm sm:items-center"
    >
        <div @click.outside="closeSalary()" class="modal-enter w-full max-w-md admin-card rounded-2xl p-6 shadow-2xl text-white">
            <h2 class="font-control text-xl font-extrabold text-white">Adjust salary</h2>
            <p class="mt-2 text-sm text-white/60">
                <span class="font-semibold text-white" x-text="salaryTarget?.name"></span>
                · current <span class="text-white" x-text="'Rs ' + Number(salaryTarget?.salary || 0).toLocaleString()"></span>
            </p>
            <form :action="baseUrl + '/' + salaryTarget?.id + '/salary'" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-white/45">Action</p>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="action in ['increase', 'decrease', 'set']" :key="action">
                            <label class="cursor-pointer">
                                <input type="radio" name="action" :value="action" class="peer sr-only" x-model="salaryAction" required>
                                <span class="flex items-center justify-center rounded-xl border border-white/15 px-2 py-2 text-xs font-semibold capitalize text-white/60 transition peer-checked:border-rose-500 peer-checked:bg-rose-600 peer-checked:text-white" x-text="action"></span>
                            </label>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Amount (Rs)</label>
                    <input type="number" name="amount" min="1" x-model="salaryAmount" required class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeSalary()" class="rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-white/60 transition hover:bg-white/10">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">Apply</button>
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
        <div @click.outside="closeDelete()" class="modal-enter w-full max-w-md admin-card rounded-2xl p-6 shadow-2xl text-white">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/15 text-rose-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h2 class="font-control text-xl font-extrabold text-white">Delete worker?</h2>
            <p class="mt-2 text-sm text-white/60">
                Remove <span class="font-semibold text-white" x-text="deleteTarget?.name"></span> from the staff list? This can’t be undone.
            </p>
            <form :action="deleteUrl" method="POST" class="mt-5 flex justify-end gap-2">
                @csrf
                <button type="button" @click="closeDelete()" class="rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-white/60 transition hover:bg-white/10">Cancel</button>
                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">Delete</button>
            </form>
        </div>
    </div>
</div>
</x-admin-layout>

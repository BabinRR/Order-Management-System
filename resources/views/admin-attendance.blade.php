<x-admin-layout title="Attendance">
<div class="space-y-5">
    <div class="flex flex-col gap-3 admin-card rounded-2xl p-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-control text-lg font-semibold text-white">Waiter attendance</h2>
            <p class="mt-0.5 text-xs text-white/45">Owner marks daily presence for active waiters</p>
        </div>
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex items-center gap-2">
            <label class="text-xs font-semibold uppercase tracking-wider text-white/45" for="date">Date</label>
            <input
                id="date"
                type="date"
                name="date"
                value="{{ $date->toDateString() }}"
                class="rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2 text-sm text-white outline-none focus:border-rose-500"
                onchange="this.form.submit()"
            >
        </form>
    </div>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="admin-card rounded-2xl p-4">
            <p class="text-xs uppercase tracking-wider text-white/40">Active waiters</p>
            <p class="font-control mt-1 text-2xl font-bold text-white">{{ $waiters->count() }}</p>
        </div>
        <div class="admin-card rounded-2xl p-4">
            <p class="text-xs uppercase tracking-wider text-white/40">Present / late / half</p>
            <p class="font-control mt-1 text-2xl font-bold text-emerald-400">{{ $presentCount }}</p>
        </div>
        <div class="admin-card rounded-2xl p-4">
            <p class="text-xs uppercase tracking-wider text-white/40">Absent</p>
            <p class="font-control mt-1 text-2xl font-bold text-rose-400">{{ $absentCount }}</p>
        </div>
    </div>

    @if ($waiters->isEmpty())
        <div class="admin-card rounded-2xl px-6 py-16 text-center text-sm text-white/45">
            No active Waiter/Waitress staff to mark. Add them under Workers first.
        </div>
    @else
        <form method="POST" action="{{ route('admin.attendance.store') }}" class="admin-card overflow-hidden rounded-2xl">
            @csrf
            <input type="hidden" name="date" value="{{ $date->toDateString() }}">

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-[#0f0f0f] text-xs uppercase tracking-wider text-white/40">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Waiter</th>
                            <th class="px-5 py-3.5 font-semibold">Shift</th>
                            <th class="px-5 py-3.5 font-semibold">Attendance</th>
                            <th class="px-5 py-3.5 font-semibold">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/8">
                        @foreach ($waiters as $index => $waiter)
                            @php
                                $row = $marked->get($waiter->id);
                                $current = old("attendances.{$index}.status", $row?->status ?? 'present');
                            @endphp
                            <tr class="hover:bg-[#0f0f0f]/70">
                                <td class="px-5 py-4">
                                    <input type="hidden" name="attendances[{{ $index }}][worker_id]" value="{{ $waiter->id }}">
                                    <p class="font-semibold text-white">{{ $waiter->name }}</p>
                                    <p class="text-xs text-white/45">{{ $waiter->role }} · Rs {{ number_format($waiter->salary) }}/mo</p>
                                </td>
                                <td class="px-5 py-4 text-white/60">{{ $waiter->shift }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($statuses as $status)
                                            <label class="cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name="attendances[{{ $index }}][status]"
                                                    value="{{ $status }}"
                                                    class="peer sr-only"
                                                    @checked($current === $status)
                                                    required
                                                >
                                                <span class="inline-flex rounded-lg border border-white/15 px-2.5 py-1.5 text-xs font-semibold capitalize text-white/55 transition peer-checked:border-rose-500 peer-checked:bg-rose-600 peer-checked:text-white">
                                                    {{ str_replace('_', ' ', $status) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <input
                                        type="text"
                                        name="attendances[{{ $index }}][note]"
                                        value="{{ old("attendances.{$index}.note", $row?->note) }}"
                                        placeholder="Optional"
                                        class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2 text-sm text-white outline-none focus:border-rose-500"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-white/10 px-5 py-4">
                <button type="submit" class="rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                    Save attendance · {{ $date->format('M j, Y') }}
                </button>
            </div>
        </form>
    @endif
</div>
</x-admin-layout>

<x-customer-layout title="Start Ordering">
<div class="mx-auto max-w-lg">
    <div class="mb-8 text-center">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-soft/45">Welcome</p>
        <h1 class="mt-2 font-display text-3xl font-extrabold tracking-tight text-ink">Order at your table</h1>
        <p class="mt-3 text-sm text-ink-soft/65">Pick your table, browse the menu, place orders, and pay your bill — all from here.</p>
    </div>

    <div class="rounded-2xl border border-[#d9cbb8] bg-white p-6 shadow-[0_8px_30px_rgba(44,33,24,0.06)] sm:p-8">
        @if (session('customer_table'))
            <div class="mb-5 rounded-xl bg-[#ede0d0] px-4 py-3 text-sm text-[#5d4037]">
                You’re at <span class="font-semibold">Table {{ session('customer_table') }}</span>
                @if (session('customer_name'))
                    · {{ session('customer_name') }}
                @endif
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('customer.menu') }}" class="flex-1 rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-3 text-center text-sm font-semibold text-white shadow-sm">
                    Continue to menu
                </a>
                <form method="POST" action="{{ route('customer.leave') }}" class="sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-[#d9cbb8] px-4 py-3 text-sm font-semibold text-ink-soft hover:bg-[#f7f0e8]">
                        Change table
                    </button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('customer.table.select') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="table_number" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Table number</label>
                    <input
                        id="table_number"
                        type="number"
                        name="table_number"
                        min="1"
                        max="50"
                        value="{{ old('table_number', 1) }}"
                        required
                        class="w-full rounded-xl border border-[#d9cbb8] px-3 py-2.5 text-sm outline-none focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/25"
                    >
                    @error('table_number') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="customer_name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Your name <span class="normal-case tracking-normal text-ink-soft/40">(optional)</span></label>
                    <input
                        id="customer_name"
                        type="text"
                        name="customer_name"
                        value="{{ old('customer_name') }}"
                        placeholder="e.g. Alex"
                        class="w-full rounded-xl border border-[#d9cbb8] px-3 py-2.5 text-sm outline-none focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/25"
                    >
                </div>
                <button type="submit" class="w-full rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                    Start ordering
                </button>
            </form>
        @endif
    </div>
</div>
</x-customer-layout>

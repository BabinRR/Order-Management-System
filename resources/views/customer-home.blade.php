<x-customer-layout title="Start Ordering">
<div class="mx-auto max-w-lg pt-4">
    <section class="mb-8 text-center">
        <h1 class="font-serif-display text-4xl font-semibold leading-tight tracking-tight text-ink sm:text-5xl">
            Welcome.
        </h1>
        <p class="font-serif-display mt-2 text-2xl font-medium text-ink-soft/55 sm:text-3xl">
            Let’s get you seated.
        </p>
        <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-ink-soft/60">
            Enter your table number to browse the menu, place orders, and settle your bill — all from your phone.
        </p>
    </section>

    <div class="overflow-hidden rounded-[1.5rem] border border-[#ebe4d8] bg-white p-6 shadow-[0_2px_12px_rgba(44,33,24,0.04)] sm:p-8">
        @if (session('customer_table'))
            <div class="mb-5 rounded-2xl bg-[#f5efe6] px-4 py-3 text-sm text-[#5d4037]">
                You’re at <span class="font-semibold">Table {{ session('customer_table') }}</span>
                @if (session('customer_name'))
                    · {{ session('customer_name') }}
                @endif
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('customer.menu') }}" class="flex-1 rounded-full bg-[#3e2723] px-4 py-3 text-center text-sm font-semibold text-white shadow-sm transition hover:bg-[#5d4037]">
                    Continue to menu
                </a>
                <form method="POST" action="{{ route('customer.leave') }}" class="sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full rounded-full border border-[#e0d8cc] px-4 py-3 text-sm font-semibold text-ink-soft transition hover:border-[#8b5e3c]">
                        Change table
                    </button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('customer.table.select') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="table_number" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Table</label>
                    <select
                        id="table_number"
                        name="table_number"
                        required
                        class="w-full rounded-2xl border border-[#e0d8cc] bg-[#f9f7f2] px-4 py-3 text-sm outline-none transition focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/20"
                    >
                        @foreach ($tables as $diningTable)
                            <option value="{{ $diningTable->number }}" @selected((int) old('table_number', 1) === $diningTable->number)>
                                Table {{ $diningTable->number }} · {{ $diningTable->seats }} seats
                            </option>
                        @endforeach
                    </select>
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
                        class="w-full rounded-2xl border border-[#e0d8cc] bg-[#f9f7f2] px-4 py-3 text-sm outline-none transition focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/20"
                    >
                </div>
                <button type="submit" class="w-full rounded-full bg-[#3e2723] px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#5d4037]">
                    Start ordering
                </button>
            </form>
        @endif
    </div>
</div>
</x-customer-layout>

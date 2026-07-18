@extends('layouts.admin')

@section('title', 'Profile Settings')
@section('eyebrow', 'Account')
@section('heading', 'Profile Settings')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    {{-- Profile summary --}}
    <section class="overflow-hidden rounded-2xl border border-[#d9cbb8] bg-white shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
        <div class="relative bg-gradient-to-br from-[#3e2723] via-[#4e342e] to-[#5d4037] px-6 py-8 text-white">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/20">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 text-xl font-extrabold ring-1 ring-white/20 backdrop-blur">
                        {{ $user->initials }}
                    </div>
                @endif
                <div>
                    <h2 class="font-display text-2xl font-extrabold tracking-tight">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-white/65">{{ $user->email }}</p>
                    <p class="mt-2 inline-flex rounded-lg bg-white/10 px-2.5 py-1 text-xs font-semibold text-white/80">
                        {{ $user->title ?: 'Admin' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Profile details --}}
    <section class="rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)] sm:p-6">
        <div class="mb-5">
            <h3 class="font-display text-lg font-extrabold text-ink">Profile details</h3>
            <p class="mt-0.5 text-sm text-ink-soft/60">Update your name, contact info, and job title.</p>
        </div>

        <form
            method="POST"
            action="{{ route('admin.profile.update') }}"
            enctype="multipart/form-data"
            class="space-y-4"
            x-data="{ preview: @js($user->avatar_url) }"
        >
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-[#d9cbb8] bg-[#f7f0e8]/70 p-4">
                <label class="mb-3 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Profile photo</label>
                <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                    <div class="relative">
                        <template x-if="preview">
                            <img :src="preview" alt="Preview" class="h-20 w-20 rounded-2xl object-cover ring-2 ring-white shadow-sm">
                        </template>
                        <template x-if="!preview">
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-[#ede0d0] text-lg font-extrabold text-[#5d4037] ring-2 ring-white shadow-sm">
                                {{ $user->initials }}
                            </div>
                        </template>
                    </div>
                    <div class="min-w-0 flex-1">
                        <input
                            id="avatar"
                            type="file"
                            name="avatar"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                            class="w-full rounded-xl border border-[#d9cbb8] bg-white px-3 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#8b5e3c] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white"
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : @js($user->avatar_url)"
                        >
                        <p class="mt-2 text-xs text-ink-soft/55">JPG, PNG, WEBP, or GIF — max 5MB.</p>
                        @error('avatar') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Full name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        @class([
                            'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                            'border-[#a0522d]' => $errors->has('name'),
                            'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('name'),
                        ])
                    >
                    @error('name') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        @class([
                            'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                            'border-[#a0522d]' => $errors->has('email'),
                            'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('email'),
                        ])
                    >
                    @error('email') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Phone</label>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        @class([
                            'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                            'border-[#a0522d]' => $errors->has('phone'),
                            'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('phone'),
                        ])
                    >
                    @error('phone') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Job title</label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title', $user->title) }}"
                        placeholder="e.g. Owner, Manager"
                        @class([
                            'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                            'border-[#a0522d]' => $errors->has('title'),
                            'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('title'),
                        ])
                    >
                    @error('title') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                    Save profile
                </button>
            </div>
        </form>
    </section>

    {{-- Password --}}
    <section class="rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)] sm:p-6">
        <div class="mb-5">
            <h3 class="font-display text-lg font-extrabold text-ink">Change password</h3>
            <p class="mt-0.5 text-sm text-ink-soft/60">Use a strong password you don’t reuse elsewhere.</p>
        </div>

        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Current password</label>
                <input
                    id="current_password"
                    type="password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    @class([
                        'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                        'border-[#a0522d]' => $errors->has('current_password'),
                        'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('current_password'),
                    ])
                >
                @error('current_password') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">New password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        @class([
                            'w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#8b5e3c]/25',
                            'border-[#a0522d]' => $errors->has('password'),
                            'border-[#d9cbb8] focus:border-[#8b5e3c]' => ! $errors->has('password'),
                        ])
                    >
                    @error('password') <p class="mt-1 text-xs font-medium text-[#a0522d]">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-ink-soft/55">Confirm password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-[#d9cbb8] px-3 py-2.5 text-sm outline-none focus:border-[#8b5e3c] focus:ring-2 focus:ring-[#8b5e3c]/25"
                    >
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-xl bg-gradient-to-br from-[#8b5e3c] to-[#5d4037] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                    Update password
                </button>
            </div>
        </form>
    </section>

    {{-- Sign out --}}
    <section class="rounded-2xl border border-[#d9cbb8] bg-white p-5 shadow-[0_1px_2px_rgba(26,31,28,0.04)] sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-display text-lg font-extrabold text-ink">Sign out</h3>
                <p class="mt-0.5 text-sm text-ink-soft/60">End your session on this device.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-xl border border-[#d9cbb8] bg-[#f5e6d8] px-4 py-2.5 text-sm font-semibold text-[#a0522d] transition hover:bg-[#edd5c4]">
                    Sign out
                </button>
            </form>
        </div>
    </section>
</div>
@endsection

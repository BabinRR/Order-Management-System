<x-admin-layout title="Profile Settings">
<div class="mx-auto max-w-3xl space-y-6">
    {{-- Profile summary --}}
    <section class="overflow-hidden admin-card rounded-2xl shadow-[0_1px_2px_rgba(26,31,28,0.04)]">
        <div class="relative bg-gradient-to-br from-[#1a1a1a] via-[#161616] to-[#121212] px-6 py-8 text-white">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/20">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 text-xl font-extrabold ring-1 ring-white/20 backdrop-blur">
                        {{ $user->initials }}
                    </div>
                @endif
                <div>
                    <h2 class="font-control text-2xl font-extrabold tracking-tight">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-white/65">{{ $user->email }}</p>
                    <p class="mt-2 inline-flex rounded-lg bg-white/10 px-2.5 py-1 text-xs font-semibold text-white/80">
                        {{ $user->title ?: 'Admin' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Profile details --}}
    <section class="admin-card rounded-2xl p-5 sm:p-6">
        <div class="mb-5">
            <h3 class="font-control text-lg font-extrabold text-white">Profile details</h3>
            <p class="mt-0.5 text-sm text-white/50">Update your name, contact info, and job title.</p>
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

            <div class="rounded-2xl border border-white/10 bg-[#0f0f0f]/70 p-4">
                <label class="mb-3 block text-xs font-semibold uppercase tracking-wider text-white/45">Profile photo</label>
                <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                    <div class="relative">
                        <template x-if="preview">
                            <img :src="preview" alt="Preview" class="h-20 w-20 rounded-2xl object-cover ring-2 ring-white shadow-sm">
                        </template>
                        <template x-if="!preview">
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-500/15 text-lg font-extrabold text-emerald-400 ring-2 ring-white shadow-sm">
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
                            class="w-full rounded-xl admin-card px-3 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-rose-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white"
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : @js($user->avatar_url)"
                        >
                        <p class="mt-2 text-xs text-white/45">JPG, PNG, WEBP, or GIF — max 5MB.</p>
                        @error('avatar') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Full name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        @class([
                            'w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25',
                            'border-rose-500' => $errors->has('name'),
                            'border-white/15 focus:border-rose-500' => ! $errors->has('name'),
                        ])
                    >
                    @error('name') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        @class([
                            'w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25',
                            'border-rose-500' => $errors->has('email'),
                            'border-white/15 focus:border-rose-500' => ! $errors->has('email'),
                        ])
                    >
                    @error('email') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Phone</label>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        @class([
                            'w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25',
                            'border-rose-500' => $errors->has('phone'),
                            'border-white/15 focus:border-rose-500' => ! $errors->has('phone'),
                        ])
                    >
                    @error('phone') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Job title</label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title', $user->title) }}"
                        placeholder="e.g. Owner, Manager"
                        @class([
                            'w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25',
                            'border-rose-500' => $errors->has('title'),
                            'border-white/15 focus:border-rose-500' => ! $errors->has('title'),
                        ])
                    >
                    @error('title') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                    Save profile
                </button>
            </div>
        </form>
    </section>

    {{-- Password --}}
    <section class="admin-card rounded-2xl p-5 sm:p-6">
        <div class="mb-5">
            <h3 class="font-control text-lg font-extrabold text-white">Change password</h3>
            <p class="mt-0.5 text-sm text-white/50">Use a strong password you don’t reuse elsewhere.</p>
        </div>

        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Current password</label>
                <input
                    id="current_password"
                    type="password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    @class([
                        'w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25',
                        'border-rose-500' => $errors->has('current_password'),
                        'border-white/15 focus:border-rose-500' => ! $errors->has('current_password'),
                    ])
                >
                @error('current_password') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">New password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        @class([
                            'w-full rounded-xl border bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-rose-500/25',
                            'border-rose-500' => $errors->has('password'),
                            'border-white/15 focus:border-rose-500' => ! $errors->has('password'),
                        ])
                    >
                    @error('password') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/45">Confirm password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-white/15 bg-[#0f0f0f] px-3 py-2.5 text-sm text-white outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/25"
                    >
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                    Update password
                </button>
            </div>
        </form>
    </section>

    {{-- Sign out --}}
    <section class="admin-card rounded-2xl p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-control text-lg font-extrabold text-white">Sign out</h3>
                <p class="mt-0.5 text-sm text-white/50">End your session on this device.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-xl border border-white/15 bg-rose-500/15 px-4 py-2.5 text-sm font-semibold text-rose-400 transition hover:bg-rose-500/25">
                    Sign out
                </button>
            </form>
        </div>
    </section>
</div>
</x-admin-layout>

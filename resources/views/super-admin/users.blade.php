@extends('layouts.app')

@section('content')
<div x-data="{
    showModal: false,
    selectedUser: null,
    activePlan: 'business',
    endsAt: '',

    openBypass(user) {
        this.selectedUser = user;
        this.activePlan = user.company?.active_plan || 'business';
        const d = new Date();
        d.setMonth(d.getMonth() + 1);
        this.endsAt = d.toISOString().split('T')[0];
        this.showModal = true;
    }
}" class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Data Pengguna & Whitelist Access</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Manajemen akun pengguna (Owner & Admin), Bypass Whitelist langganan, dan hapus pengguna.</p>
        </div>
    </div>

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Pengguna</span>
            <h3 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</h3>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Terverifikasi</span>
            <h3 class="mt-1 text-xl font-bold text-success-600 dark:text-success-400">{{ number_format($stats['verified']) }}</h3>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Owner Perusahaan</span>
            <h3 class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-400">{{ number_format($stats['owners']) }}</h3>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Admin Staff</span>
            <h3 class="mt-1 text-xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['admins']) }}</h3>
        </div>
    </div>

    <!-- Search & Filter Controls Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="GET" action="{{ route('super-admin.users') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari berdasarkan nama, email, atau perusahaan..."
                    class="h-10 w-full rounded-xl border border-gray-200 bg-transparent px-4 pl-10 text-xs text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500"
                />
                <svg class="absolute left-3 top-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select
                name="role"
                onchange="this.form.submit()"
                class="h-10 rounded-xl border border-gray-200 bg-transparent px-3 text-xs text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
            >
                <option value="">Semua Role (Owner & Admin)</option>
                <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <button type="submit" class="h-10 rounded-xl bg-brand-500 px-4 text-xs font-semibold text-white transition hover:bg-brand-600">
                Filter
            </button>
            @if (request('search') || request('role'))
                <a href="{{ route('super-admin.users') }}" class="h-10 flex items-center justify-center rounded-xl border border-gray-200 px-3 text-xs text-gray-600 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Users Full Width Table Card -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-gray-200 bg-gray-50/80 text-gray-500 uppercase dark:border-gray-800 dark:bg-gray-900/80 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Pengguna</th>
                        <th class="px-5 py-3 font-semibold">Perusahaan</th>
                        <th class="px-5 py-3 font-semibold">Role</th>
                        <th class="px-5 py-3 font-semibold">Status Paket</th>
                        <th class="px-5 py-3 font-semibold">Batas Akses / Expired</th>
                        <th class="px-5 py-3 text-center font-semibold">Aksi Whitelist & Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-500/10 font-bold text-brand-600 text-xs dark:text-brand-400">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->company?->name ?? 'Tanpa Perusahaan' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($user->role === 'owner')
                                    <span class="inline-flex rounded-md bg-brand-100 px-2 py-0.5 text-[10px] font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">OWNER</span>
                                @else
                                    <span class="inline-flex rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ strtoupper($user->role) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($user->company?->active_plan && $user->company?->subscription_ends_at?->isFuture())
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                        </svg>
                                        {{ str($user->company->active_plan)->headline() }}
                                    </span>
                                @elseif ($user->company?->onTrial())
                                    <span class="inline-flex rounded-md bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                        TRIAL (14 Hari)
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Gratis / Expired</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">
                                @if ($user->company?->subscription_ends_at)
                                    <div class="flex flex-col">
                                        <span class="{{ $user->company->subscription_ends_at->isFuture() ? 'text-gray-900 font-bold dark:text-white' : 'text-red-500 line-through' }}">
                                            {{ $user->company->subscription_ends_at->format('d M Y') }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">
                                            {{ $user->company->subscription_ends_at->isFuture() ? $user->company->subscription_ends_at->diffForHumans() : 'Sudah Expired' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($user->company)
                                        <button
                                            type="button"
                                            @click="openBypass({
                                                id: {{ $user->id }},
                                                name: '{{ e($user->name) }}',
                                                company_name: '{{ e($user->company->name) }}',
                                                company: {
                                                    active_plan: '{{ $user->company->active_plan }}'
                                                }
                                            })"
                                            class="inline-flex items-center gap-1 rounded-xl bg-purple-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-purple-700 transition"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                            Bypass
                                        </button>

                                        @if ($user->company->active_plan)
                                            <form method="POST" action="{{ route('super-admin.users.revoke-bypass', $user) }}" onsubmit="return confirm('Cabut akses whitelist untuk user ini?')">
                                                @csrf
                                                <button type="submit" title="Cabut Bypass" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-2 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
                                                    Cabut
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <!-- Delete User Button -->
                                    <form method="POST" action="{{ route('super-admin.users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ e($user->name) }}? Aksi ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Pengguna" class="inline-flex items-center gap-1 rounded-xl bg-red-500 px-2.5 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-red-600 transition">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                Tidak ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Whitelist Bypass Modal -->
    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-xs"
    >
        <div
            @click.away="showModal = false"
            class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-800 dark:bg-gray-900"
        >
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Bypass Whitelist Pengguna</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedUser ? selectedUser.name + ' (' + selectedUser.company_name + ')' : ''"></p>
                </div>
                <button @click="showModal = false" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'/super-admin/users/' + (selectedUser ? selectedUser.id : '') + '/grant-bypass'" method="POST" class="mt-4 space-y-4">
                @csrf
                <!-- Select Package -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Paket Langganan</label>
                    <select
                        name="active_plan"
                        x-model="activePlan"
                        class="w-full rounded-xl border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                        required
                    >
                        <option value="starter">Starter (100 Klien, 50 Document/Bln)</option>
                        <option value="business">Business (500 Klien, 500 Invoice)</option>
                        <option value="enterprise">Enterprise (Unlimited Klien & Dokumen)</option>
                    </select>
                </div>

                <!-- Expiry Date Limit -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Batas Tanggal Berakhir Whitelist</label>
                    <input
                        type="date"
                        name="subscription_ends_at"
                        x-model="endsAt"
                        class="w-full rounded-xl border border-gray-200 bg-transparent px-3 py-2 text-xs text-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                        required
                    />
                    <p class="mt-1 text-[11px] text-gray-400">Pengguna akan mendapat akses gratis tanpa bayar hingga tanggal ini.</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-purple-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-purple-700 transition"
                    >
                        Simpan & Aktifkan Whitelist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

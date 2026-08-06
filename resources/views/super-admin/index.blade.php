@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard Super Admin</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Ringkasan aktivitas platform, pengguna aktif, dan pengajuan langganan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('super-admin.users') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Kelola Pengguna
            </a>
            <a href="{{ route('super-admin.reports') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Lihat Laporan
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Pengguna</span>
                    <h3 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_users']) }}</h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400"><span class="font-semibold text-gray-700 dark:text-gray-300">{{ $stats['total_companies'] }}</span> perusahaan terdaftar</p>
        </div>

        <!-- Active Subscriptions -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Langganan Aktif</span>
                    <h3 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active_subscriptions']) }}</h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Paket aktif terverifikasi</p>
        </div>

        <!-- Pending Submissions -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Menunggu Konfirmasi</span>
                    <h3 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['pending_submissions']) }}</h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-warning-600 dark:text-warning-400 font-medium">Perlu penanganan admin</p>
        </div>

        <!-- Total Revenue -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</span>
                    <h3 class="mt-1 text-xl font-bold text-gray-900 dark:text-white"><x-money :amount="$stats['total_revenue']" /></h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Dari langganan billing</p>
        </div>
    </div>

    <!-- Section 1: Billing Submissions Queue Datatable with Pagination -->
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 pb-4 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Pengajuan & Aktivasi Billing Perusahaan</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelola, verifikasi, aktifkan, atau hapus pengajuan langganan perusahaan.</p>
                </div>
                <a href="{{ route('super-admin.reports') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Lihat Laporan Lengkap &rarr;</a>
            </div>

            <!-- Datatable Search & Filter Form -->
            <form method="GET" action="{{ route('super-admin.index') }}" class="flex flex-col sm:flex-row gap-3 pt-2">
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama perusahaan..."
                        class="h-9 w-full rounded-xl border border-gray-200 bg-transparent px-3 pl-9 text-xs text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                    />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="h-9 rounded-xl border border-gray-200 bg-transparent px-3 text-xs text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                >
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi (Aktif)</option>
                    <option value="stopped" {{ request('status') === 'stopped' ? 'selected' : '' }}>Dihentikan</option>
                </select>

                <button type="submit" class="h-9 rounded-xl bg-brand-500 px-4 text-xs font-semibold text-white transition hover:bg-brand-600">
                    Cari
                </button>
                @if (request('search') || request('status'))
                    <a href="{{ route('super-admin.index') }}" class="h-9 flex items-center justify-center rounded-xl border border-gray-200 px-3 text-xs text-gray-600 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-y border-gray-200 bg-gray-50/80 text-gray-500 uppercase dark:border-gray-800 dark:bg-gray-900/80 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Perusahaan</th>
                        <th class="px-5 py-3 font-semibold">Paket</th>
                        <th class="px-5 py-3 font-semibold">Periode</th>
                        <th class="px-5 py-3 font-semibold">Jumlah</th>
                        <th class="px-5 py-3 font-semibold">Metode</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($recentSubmissions as $submission)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white">
                                {{ $submission->company?->name ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-3.5 font-medium text-brand-600 dark:text-brand-400">
                                {{ str($submission->package)->headline() }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ ($submission->billing_period ?? 'monthly') === 'yearly' ? 'Tahunan' : 'Bulanan' }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white">
                                <x-money :amount="$submission->amount" />
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ str_replace('_', ' ', strtoupper($submission->payment_method)) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <x-status-badge :status="$submission->status" />
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">
                                {{ $submission->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($submission->status !== 'confirmed')
                                        <form method="POST" action="{{ route('super-admin.billing.activate', $submission) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="inline-flex items-center gap-1 rounded-xl bg-brand-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-brand-600 shadow-sm">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Aktifkan
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('super-admin.billing.stop', $submission) }}" onsubmit="return confirm('Hentikan billing perusahaan ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="inline-flex items-center gap-1 rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-600 shadow-sm">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Hentikan
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Submission Button -->
                                    <form method="POST" action="{{ route('super-admin.billing.destroy', $submission) }}" onsubmit="return confirm('Hapus pengajuan billing ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Pengajuan" class="inline-flex items-center justify-center rounded-xl bg-red-500 p-1.5 text-xs font-semibold text-white transition hover:bg-red-600 shadow-sm">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                Belum ada data pengajuan billing.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            {{ $recentSubmissions->links() }}
        </div>
    </section>

    <!-- Section 2: Recent Users List -->
    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Pengguna Terbaru Bergabung</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Daftar pengguna yang baru mendaftar di aplikasi.</p>
            </div>
            <a href="{{ route('super-admin.users') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Kelola Semua Pengguna &rarr;</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach ($latestUsers as $user)
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3.5 dark:border-gray-800 dark:bg-gray-900/50 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600 font-bold text-sm uppercase dark:text-brand-400">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</h4>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                        <div class="mt-2 flex items-center justify-between text-[10px]">
                            <span class="rounded-md bg-gray-200 px-1.5 py-0.5 font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300 uppercase">{{ $user->role }}</span>
                            <span class="text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

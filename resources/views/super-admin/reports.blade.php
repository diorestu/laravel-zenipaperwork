@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laporan & Transaksi Langganan</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Laporan pendapatan platform, distribusi paket langganan, dan riwayat transaksi pengajuan billing.</p>
        </div>
    </div>

    <!-- Revenue & Package Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Pendapatan Terkonfirmasi</span>
            <h3 class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400"><x-money :amount="$totalRevenue" /></h3>
            <p class="mt-2 text-[11px] text-gray-400">Seluruh transaksi terkonfirmasi</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Pendapatan Bulan Ini</span>
            <h3 class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400"><x-money :amount="$monthlyRevenue" /></h3>
            <p class="mt-2 text-[11px] text-gray-400">{{ now()->format('F Y') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Paket Business Aktif</span>
            <h3 class="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($packageDistribution['business']) }}</h3>
            <p class="mt-2 text-[11px] text-gray-400">Perusahaan berlangganan</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Paket Enterprise Aktif</span>
            <h3 class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($packageDistribution['enterprise']) }}</h3>
            <p class="mt-2 text-[11px] text-gray-400">Perusahaan skala besar</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="GET" action="{{ route('super-admin.reports') }}" class="flex flex-col sm:flex-row gap-3">
            <select
                name="status"
                onchange="this.form.submit()"
                class="h-10 rounded-xl border border-gray-200 bg-transparent px-3 text-xs text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
            >
                <option value="">Semua Status Transaksi</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi (Aktif)</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                <option value="stopped" {{ request('status') === 'stopped' ? 'selected' : '' }}>Dihentikan / Kadaluarsa</option>
            </select>

            <select
                name="package"
                onchange="this.form.submit()"
                class="h-10 rounded-xl border border-gray-200 bg-transparent px-3 text-xs text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
            >
                <option value="">Semua Paket</option>
                <option value="starter" {{ request('package') === 'starter' ? 'selected' : '' }}>Starter</option>
                <option value="business" {{ request('package') === 'business' ? 'selected' : '' }}>Business</option>
                <option value="enterprise" {{ request('package') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
            </select>

            <button type="submit" class="h-10 rounded-xl bg-brand-500 px-4 text-xs font-semibold text-white transition hover:bg-brand-600">
                Filter Laporan
            </button>
            @if (request('status') || request('package'))
                <a href="{{ route('super-admin.reports') }}" class="h-10 flex items-center justify-center rounded-xl border border-gray-200 px-3 text-xs text-gray-600 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Reports Full Width Table Card -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-gray-200 bg-gray-50/80 text-gray-500 uppercase dark:border-gray-800 dark:bg-gray-900/80 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Perusahaan</th>
                        <th class="px-5 py-3 font-semibold">Paket</th>
                        <th class="px-5 py-3 font-semibold">Periode</th>
                        <th class="px-5 py-3 font-semibold">Nominal</th>
                        <th class="px-5 py-3 font-semibold">Metode Pembayaran</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Tanggal Pengajuan</th>
                        <th class="px-5 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($submissions as $sub)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="px-5 py-3.5 font-bold text-gray-900 dark:text-white">
                                {{ $sub->company?->name ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-brand-600 dark:text-brand-400">
                                {{ str($sub->package)->headline() }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ ($sub->billing_period ?? 'monthly') === 'yearly' ? 'Tahunan' : 'Bulanan' }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-gray-900 dark:text-white">
                                <x-money :amount="$sub->amount" />
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ str_replace('_', ' ', strtoupper($sub->payment_method)) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <x-status-badge :status="$sub->status" />
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">
                                {{ $sub->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <form method="POST" action="{{ route('super-admin.billing.destroy', $sub) }}" onsubmit="return confirm('Hapus pengajuan billing ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Transaksi" class="inline-flex items-center gap-1 rounded-xl bg-red-500 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-red-600 shadow-xs">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                Belum ada laporan transaksi langganan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
@endsection

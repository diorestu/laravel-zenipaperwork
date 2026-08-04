@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Verifikasi Pengguna</h1>
        <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($users as $user)
                <div class="flex justify-between py-3 text-sm text-gray-700 dark:text-gray-300"><span>{{ $user->name }}</span><span>{{ $user->role }}</span></div>
            @endforeach
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Kelola Billing User</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aktifkan atau hentikan paket billing untuk setiap perusahaan.</p>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-100 text-xs uppercase text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold">Paket</th>
                        <th class="px-4 py-3 font-semibold">Periode</th>
                        <th class="px-4 py-3 font-semibold">Jumlah</th>
                        <th class="px-4 py-3 font-semibold">Metode</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($billingSubmissions as $submission)
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900 dark:text-white/90">
                                {{ $submission->company?->name ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">{{ str($submission->package)->headline() }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ ($submission->billing_period ?? 'monthly') === 'yearly' ? 'Tahunan' : 'Bulanan' }}</td>
                            <td class="whitespace-nowrap px-4 py-3"><x-money :amount="$submission->amount" /></td>
                            <td class="whitespace-nowrap px-4 py-3">{{ str_replace('_', ' ', $submission->payment_method) }}</td>
                            <td class="whitespace-nowrap px-4 py-3"><x-status-badge :status="$submission->status" /></td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-500 dark:text-gray-400">{{ $submission->created_at->format('d M Y H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($submission->status !== 'confirmed')
                                        <form method="POST" action="{{ route('super-admin.billing.activate', $submission) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-600">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    @if ($submission->status === 'confirmed')
                                        <form method="POST" action="{{ route('super-admin.billing.stop', $submission) }}" onsubmit="return confirm('Hentikan billing user ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg bg-error-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-error-600">
                                                Hentikan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data billing.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

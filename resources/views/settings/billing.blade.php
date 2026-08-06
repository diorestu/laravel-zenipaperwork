@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Tagihan & Langganan</h1>
        @if (request()->has('from_mobile') || str_contains(request()->header('referer', ''), '/mobile'))
            <a href="{{ route('mobile.app') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3.5 py-2 text-xs font-semibold text-white shadow-theme-xs hover:bg-brand-600">
                ← Kembali ke Mobile App
            </a>
        @endif
    </div>

    @if ($onTrial)
        <!-- Banner Uji Coba Gratis -->
        <div class="relative overflow-hidden rounded-2xl border-l-4 border-brand-500 border-y border-r border-gray-200 bg-linear-to-r from-brand-500/5 to-indigo-500/5 p-5 shadow-theme-xs dark:border-gray-800 dark:from-brand-500/10 dark:to-indigo-500/10">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500 text-white shadow-theme-xs">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Masa Uji Coba Gratis 30 Hari Aktif</h2>
                    <p class="mt-1 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                        Anda sedang menikmati akses uji coba gratis fitur paket <strong class="text-brand-600 dark:text-brand-400">Business</strong>. 
                        Masa uji coba gratis Anda akan berakhir pada <strong class="text-gray-800 dark:text-white">{{ $trialEndsAt->locale('id')->translatedFormat('d F Y') }}</strong> 
                        (<span class="font-semibold text-brand-600 dark:text-brand-400">{{ $trialDaysRemaining }} hari lagi</span>). 
                        Anda dapat memilih dan meningkatkan paket langganan Anda kapan saja di bawah ini.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @php
        $pendingSubmission = $submissions->where('status', 'pending')->first();
        $planLevels = ['starter' => 1, 'business' => 2, 'enterprise' => 3];
        $currentLevel = ($activePlan && isset($planLevels[$activePlan])) ? $planLevels[$activePlan] : 0;
    @endphp

    @if ($pendingSubmission)
        <!-- Banner Pending Submission -->
        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-amber-900 shadow-xs dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xs">
                    <p class="font-bold text-amber-950 dark:text-amber-200 text-sm">Pengajuan Billing Sedang Diproses Admin</p>
                    <p class="mt-0.5 text-amber-800 dark:text-amber-300">
                        Pengajuan paket <strong class="uppercase">{{ $pendingSubmission->package }}</strong> (<x-money :amount="$pendingSubmission->amount" />) pada {{ $pendingSubmission->created_at->format('d M Y H:i') }} sedang menunggu verifikasi admin. Anda tidak dapat membuat pengajuan baru sampai transaksi selesai.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div x-data="{ billingPeriod: 'monthly' }" class="space-y-6">
    <div class="flex justify-end">
        <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <button type="button" @click="billingPeriod = 'monthly'" :class="billingPeriod === 'monthly' ? 'bg-brand-500 text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'" class="rounded-md px-4 py-2 text-sm font-semibold transition">
                Bulanan
            </button>
            <button type="button" @click="billingPeriod = 'yearly'" :class="billingPeriod === 'yearly' ? 'bg-brand-500 text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'" class="rounded-md px-4 py-2 text-sm font-semibold transition">
                Tahunan
                <span class="ml-1 text-[11px]" :class="billingPeriod === 'yearly' ? 'text-white/80' : 'text-success-600 dark:text-success-400'">-10%</span>
            </button>
        </div>
    </div>

    <section class="grid gap-4 lg:grid-cols-3">
        @foreach ($plans as $plan)
            @php($isActive = $activePlan === $plan['slug'])
            @php($isTrialActive = $onTrial && $plan['slug'] === 'business' && !$activePlan)
            @php($isHighlighted = $isActive || $isTrialActive)
            @php($yearlyAmount = (int) round($plan['amount'] * 12 * 0.9))
            @php($targetLevel = $planLevels[$plan['slug']] ?? 0)
            @php($canUpgrade = ($currentLevel === 0) || ($targetLevel > $currentLevel))
            <article @class([
                'flex h-full flex-col rounded-lg border bg-white p-5 shadow-theme-xs dark:bg-white/[0.03]',
                'border-brand-500 ring-2 ring-brand-500/15 dark:border-brand-400 dark:ring-brand-400/20' => $isHighlighted,
                'border-gray-200 dark:border-gray-800' => ! $isHighlighted,
            ])>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">{{ $plan['name'] }}</h2>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white/90">
                            <span x-show="billingPeriod === 'monthly'"><x-money :amount="$plan['amount']" /></span>
                            <span x-show="billingPeriod === 'yearly'" style="display: none;"><x-money :amount="$yearlyAmount" /></span>
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span x-show="billingPeriod === 'monthly'">per bulan</span>
                            <span x-show="billingPeriod === 'yearly'" style="display: none;">per tahun, sudah termasuk diskon 10%</span>
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="rounded-full border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600 dark:border-gray-700 dark:text-gray-300" x-text="billingPeriod === 'monthly' ? 'Bulanan' : 'Tahunan'">Bulanan</span>
                        @if ($isActive)
                            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">Paket Aktif</span>
                        @elseif ($isTrialActive)
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 animate-pulse">Uji Coba Aktif</span>
                        @endif
                    </div>
                </div>

                <div class="mt-5 flex-1 border-t border-gray-100 pt-4 pb-6 dark:border-gray-800">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Layanan yang didapat</p>
                    <ul class="mt-3 space-y-1.5 text-xs leading-5 text-gray-600 dark:text-gray-300">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex gap-2">
                                <span class="mt-0.5 inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400">
                                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3.5 8.2L6.4 11L12.5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($isActive)
                    <button type="button" disabled class="mt-auto w-full rounded-lg border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm font-semibold text-brand-700 cursor-not-allowed dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-300">
                        Paket Aktif Saat Ini
                    </button>
                @elseif ($pendingSubmission)
                    <button type="button" disabled class="mt-auto w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-500">
                        Pengajuan Diproses
                    </button>
                @elseif (!$canUpgrade)
                    <button type="button" disabled class="mt-auto w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-500">
                        Hanya Untuk Upgrade
                    </button>
                @else
                    <button type="button" @click="$dispatch('open-modal', 'confirm-payment-{{ $plan['slug'] }}')" class="mt-auto w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:border-brand-500 hover:bg-brand-500 hover:text-white dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-200 dark:hover:border-brand-500 dark:hover:bg-brand-500 dark:hover:text-white">
                        Upgrade Paket Ini
                    </button>
                @endif
            </article>

            <x-ui.modal name="confirm-payment-{{ $plan['slug'] }}" class="max-w-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white/90">Konfirmasi Langganan Paket</h2>
                <div class="mt-4 rounded-lg border border-gray-200 p-4 dark:border-gray-800 bg-gray-50 dark:bg-white/[0.01]">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Paket Langganan:</span>
                        <span class="font-bold text-gray-900 dark:text-white/90">{{ $plan['name'] }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm border-t border-gray-200 dark:border-gray-800 pt-2">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Total Biaya:</span>
                        <span class="font-bold text-brand-600 dark:text-brand-400">
                            <span x-show="billingPeriod === 'monthly'"><x-money :amount="$plan['amount']" /></span>
                            <span x-show="billingPeriod === 'yearly'" style="display: none;"><x-money :amount="$yearlyAmount" /></span>
                        </span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm border-t border-gray-200 dark:border-gray-800 pt-2">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Periode:</span>
                        <span class="font-bold text-gray-900 dark:text-white/90" x-text="billingPeriod === 'monthly' ? 'Bulanan' : 'Tahunan'">Bulanan</span>
                    </div>
                    <div class="mt-3 border-t border-gray-200 pt-3 dark:border-gray-800">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Detail layanan:</p>
                        <ul class="mt-2 space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex gap-1.5">
                                    <span class="text-success-600 dark:text-success-400">✓</span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <form method="POST" action="{{ route('billing.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4" x-data="{ paymentMethod: 'qris' }">
                    @csrf
                    <input type="hidden" name="package" value="{{ $plan['slug'] }}">
                    <input type="hidden" name="billing_period" :value="billingPeriod">
                    <input type="hidden" name="amount" :value="billingPeriod === 'yearly' ? {{ $yearlyAmount }} : {{ $plan['amount'] }}">

                    <div class="rounded-lg border border-gray-200 p-3 text-sm dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Periode</span>
                            <span class="font-semibold text-gray-900 dark:text-white/90" x-text="billingPeriod === 'monthly' ? 'Bulanan' : 'Tahunan'">Bulanan</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-gray-100 pt-2 dark:border-gray-800">
                            <span class="text-gray-500 dark:text-gray-400">Total bayar</span>
                            <span class="font-bold text-brand-600 dark:text-brand-400">
                                <span x-show="billingPeriod === 'monthly'"><x-money :amount="$plan['amount']" /></span>
                                <span x-show="billingPeriod === 'yearly'" style="display: none;"><x-money :amount="$yearlyAmount" /></span>
                            </span>
                        </div>
                    </div>

                    <!-- Pilihan Metode Pembayaran -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Opsi QRIS -->
                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition-all duration-200"
                                :class="paymentMethod === 'qris' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/10' : 'border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.01]'">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-gray-900 dark:text-white">QRIS (Otomatis)</span>
                                    <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-brand-600 focus:ring-brand-500">
                                </div>
                                <span class="text-[10px] text-gray-500">Pembayaran instan, aktif otomatis dalam 1 menit.</span>
                            </label>

                            <!-- Opsi Transfer Manual -->
                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition-all duration-200"
                                :class="paymentMethod === 'manual_transfer' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/10' : 'border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.01]'">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-gray-900 dark:text-white">Transfer Manual</span>
                                    <input type="radio" name="payment_method" value="manual_transfer" x-model="paymentMethod" class="text-brand-600 focus:ring-brand-500">
                                </div>
                                <span class="text-[10px] text-gray-500">Kirim bukti transfer, verifikasi admin maksimal 1x24 jam.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Detail QRIS Info -->
                    <div x-show="paymentMethod === 'qris'" x-transition class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 rounded-lg text-xs leading-relaxed flex gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 18.333A8.333 8.333 0 1 0 10 1.667a8.333 8.333 0 0 0 0 16.666Z" stroke="currentColor" stroke-width="1.5" />
                            <path d="M10 9.167v4.166M10 6.667h.008" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                        </svg>
                        <span>Setelah menekan tombol Konfirmasi, Anda akan diarahkan ke halaman invoice billing yang memuat kode QRIS untuk pembayaran langsung.</span>
                    </div>

                    <!-- Detail Transfer Manual -->
                    <div x-show="paymentMethod === 'manual_transfer'" x-transition class="space-y-4" style="display: none;">
                        <!-- Rekening Perusahaan -->
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-300 rounded-lg text-xs leading-relaxed">
                            <p class="font-bold mb-1">Rekening Tujuan Transfer:</p>
                            <p>Bank Mandiri</p>
                            <p class="font-bold text-sm select-all">1450018446365</p>
                            <p class="mt-0.5">a/n PT Numa Teknologi Nusantara</p>
                        </div>

                        <!-- Bukti Transfer -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Unggah Bukti Transfer</label>
                            <input type="file" name="proof" accept="image/*,application/pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-gray-800 dark:file:text-gray-300">
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" rows="2" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Misalnya nama pengirim rekening bank Anda..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-4">
                        <button type="button" @click="$dispatch('close-modal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-750 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-850 cursor-pointer">Batal</button>
                        <button class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 cursor-pointer">Konfirmasi & Lanjutkan</button>
                    </div>
                </form>
            </x-ui.modal>
        @endforeach
    </section>
    </div>

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">Riwayat Pembayaran</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                <thead class="bg-gray-50 dark:bg-white/[0.02]">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Paket</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Periode</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Metode</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Jumlah</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Status</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($submissions as $submission)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="whitespace-nowrap px-4 py-2.5 font-medium text-gray-900 dark:text-white">
                                {{ str($submission->package)->headline() }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-500 dark:text-gray-400">
                                {{ ($submission->billing_period ?? 'monthly') === 'yearly' ? 'Tahunan' : 'Bulanan' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-500 dark:text-gray-400 uppercase">
                                {{ str_replace('_', ' ', $submission->payment_method) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-900 dark:text-white font-medium">
                                <x-money :amount="$submission->amount" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5">
                                <x-status-badge :status="$submission->status" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-500 dark:text-gray-400">
                                {{ $submission->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('settings.billing.show', $submission) }}" 
                                       class="inline-flex items-center gap-1 rounded bg-gray-100 px-2 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 dark:bg-gray-850 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer">
                                        <i class="bx bx-show text-sm"></i>
                                        <span>Rincian</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                Belum ada riwayat pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

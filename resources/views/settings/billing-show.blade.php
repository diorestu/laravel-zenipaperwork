@extends('layouts.app')

@section('content')
@php
    $payload = $submission->payment_payload ?? [];
    $qrisImage = data_get($payload, 'qris_url')
        ?? data_get($payload, 'qr_url')
        ?? data_get($payload, 'qr_image')
        ?? data_get($payload, 'qris_image')
        ?? data_get($payload, 'qris.image')
        ?? data_get($payload, 'data.qris_url')
        ?? data_get($payload, 'data.qr_url')
        ?? data_get($payload, 'data.qr_image')
        ?? data_get($payload, 'data.qris_image');
@endphp

<div class="space-y-6"
     x-data="{
        status: @js($submission->status),
        checking: false,
        timer: null,
        isConfirmed: @js($submission->status === 'confirmed'),
        init() {
            if (this.isConfirmed) return;
            this.timer = setInterval(() => {
                this.checkStatusDebounced();
            }, 5000);
        },
        async checkStatusDebounced() {
            if (this.checking || this.isConfirmed) return;
            this.checking = true;
            try {
                let res = await fetch(@js(route('settings.billing.check-status', $submission)));
                let data = await res.json();
                if (data.is_confirmed) {
                    this.isConfirmed = true;
                    this.status = 'confirmed';
                    clearInterval(this.timer);
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (e) {}
            this.checking = false;
        }
     }"
>
    <!-- Alert Banner Status Verification -->
    <template x-if="isConfirmed">
        <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-sm text-success-800 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2 font-semibold">
                <svg class="h-5 w-5 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Pembayaran Berhasil Diverifikasi! Paket {{ str($submission->package)->headline() }} Telah Aktif.</span>
            </div>
            <span class="text-xs opacity-75">Memuat ulang data...</span>
        </div>
    </template>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Pembayaran QRIS Pakasir</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pembayaran Paket {{ str($submission->package)->headline() }}</p>
        </div>
        <a href="{{ request()->has('from_mobile') || str_contains(request()->header('referer', ''), '/mobile') ? route('mobile.app') : route('settings.billing') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
            ← {{ request()->has('from_mobile') || str_contains(request()->header('referer', ''), '/mobile') ? 'Kembali ke Mobile App' : 'Kembali ke Billing' }}
        </a>
    </div>

    <section class="grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] space-y-4">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Rincian Transaksi</h2>
            <dl class="grid gap-4 text-xs sm:grid-cols-2">
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-900/50">
                    <dt class="text-gray-500 dark:text-gray-400">Paket Langganan</dt>
                    <dd class="mt-1 font-bold text-gray-900 text-sm dark:text-white">{{ str($submission->package)->headline() }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-900/50">
                    <dt class="text-gray-500 dark:text-gray-400">Periode Tagihan</dt>
                    <dd class="mt-1 font-bold text-gray-900 text-sm dark:text-white">{{ ($submission->billing_period ?? 'monthly') === 'yearly' ? 'Tahunan' : 'Bulanan' }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-900/50">
                    <dt class="text-gray-500 dark:text-gray-400">Total Nominal Pembayaran</dt>
                    <dd class="mt-1 font-bold text-brand-600 text-base dark:text-brand-400"><x-money :amount="$submission->amount" /></dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-900/50">
                    <dt class="text-gray-500 dark:text-gray-400">Metode Pembayaran</dt>
                    <dd class="mt-1 font-bold text-gray-900 dark:text-white uppercase">{{ str_replace('_', ' ', $submission->payment_method) }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-900/50">
                    <dt class="text-gray-500 dark:text-gray-400">Status Transaksi</dt>
                    <dd class="mt-1 flex items-center gap-2">
                        <template x-if="isConfirmed">
                            <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-semibold text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-success-500"></span> Terkonfirmasi
                            </span>
                        </template>
                        <template x-if="!isConfirmed">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                <span class="h-1.5 w-1.5 animate-ping rounded-full bg-amber-500"></span> Menunggu Pembayaran
                            </span>
                        </template>
                    </dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-900/50">
                    <dt class="text-gray-500 dark:text-gray-400">ID Pesanan Transaksi</dt>
                    <dd class="mt-1 font-bold text-gray-900 dark:text-white">{{ $submission->payment_order_id ?? ('PAPERWORK-B' . str_pad((string) $submission->id, 5, '0', STR_PAD_LEFT)) }}</dd>
                </div>
            </dl>

            <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-4 text-xs text-blue-800 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                <p class="font-bold text-sm mb-1">💡 Petunjuk Pembayaran QRIS Pakasir:</p>
                <ol class="list-decimal pl-4 space-y-1">
                    <li>Buka aplikasi mobile banking atau e-wallet (BCA, Mandiri, GoPay, OVO, Dana, ShopeePay, dll).</li>
                    <li>Pilih menu <strong>Scan / Pindai QRIS</strong> lalu arahkan kamera ke Kode QRIS di sebelah kanan.</li>
                    <li>Pastikan nama merchant dan nominal sesuai dengan jumlah di atas.</li>
                    <li>Selesaikan pembayaran. Sistem mengecek status verifikasi secara otomatis tiap 5 detik.</li>
                </ol>
            </div>
        </div>

        <aside class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white text-center">Kode QRIS Pakasir</h2>
                <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-1">Scan dengan E-Wallet / Bank Mandiri / BCA / Dana</p>

                @if ($paymentQrCode)
                    <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700">
                        <img src="{{ $paymentQrCode }}" alt="QRIS Pakasir" class="mx-auto aspect-square w-full max-w-64 object-contain">
                    </div>
                @elseif ($qrisImage)
                    <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700">
                        <img src="{{ $qrisImage }}" alt="QRIS Pakasir" class="mx-auto aspect-square w-full max-w-64 object-contain">
                    </div>
                @else
                    <p class="mt-4 rounded-xl border border-gray-200 p-4 text-center text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">QRIS sedang dimuat...</p>
                @endif
            </div>

            <div class="mt-6 space-y-2">
                <!-- Status Auto Check Indicator -->
                <div class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400 py-1" x-show="!isConfirmed">
                    <span class="h-2 w-2 rounded-full bg-brand-500 animate-ping"></span>
                    <span>Pemeriksaan otomatis background (setiap 5 detik)</span>
                </div>

                @if ($submission->payment_url)
                    <a href="{{ $submission->payment_url }}" target="_blank" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-brand-600 transition">
                        <span>Buka Halaman Pembayaran Pakasir</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                @endif
            </div>
        </aside>
    </section>
</div>
@endsection

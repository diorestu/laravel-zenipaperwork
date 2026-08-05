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

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Pembayaran QRIS</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ str($submission->package)->headline() }}</p>
        </div>
        <a href="{{ request()->has('from_mobile') || str_contains(request()->header('referer', ''), '/mobile') ? route('mobile.app') : route('settings.billing') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-white/[0.03]">
            ← {{ request()->has('from_mobile') || str_contains(request()->header('referer', ''), '/mobile') ? 'Kembali ke Mobile App' : 'Kembali' }}
        </a>
    </div>

    <section class="grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">Pembayaran</h2>
            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Paket</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white/90">{{ str($submission->package)->headline() }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Periode</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white/90">{{ ($submission->billing_period ?? 'monthly') === 'yearly' ? 'Tahunan' : 'Bulanan' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Jumlah</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white/90"><x-money :amount="$submission->amount" /></dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Metode</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white/90">{{ str($submission->payment_method)->headline() }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1"><x-status-badge :status="$submission->status" /></dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">ID Pesanan</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white/90">{{ $submission->payment_order_id ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Nomor Pembayaran</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white/90">{{ $submission->payment_number ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <aside class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">QRIS Pakasir</h2>
            @if ($paymentQrCode)
                <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700">
                    <img src="{{ $paymentQrCode }}" alt="QRIS Pakasir" class="mx-auto aspect-square w-full max-w-64 object-contain">
                </div>
            @elseif ($qrisImage)
                <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700">
                    <img src="{{ $qrisImage }}" alt="QRIS Pakasir" class="mx-auto aspect-square w-full max-w-64 object-contain">
                </div>
            @else
                <p class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">QRIS belum tersedia dari Pakasir.</p>
            @endif

            @if ($submission->payment_url)
                <a href="{{ $submission->payment_url }}" target="_blank" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    Link Pembayaran
                </a>
            @endif
        </aside>
    </section>
</div>
@endsection

@extends('layouts.fullscreen-layout')

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

<div class="min-h-screen bg-[#F7F6F3] pb-12 text-gray-900 dark:bg-gray-950 dark:text-white">
    <!-- Top Header App Bar -->
    <header class="sticky top-0 z-40 border-b border-gray-200/80 bg-white/95 px-4 py-4 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-900/95">
        <div class="mx-auto flex max-w-md items-center justify-between">
            <a href="{{ route('mobile.app') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">
                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Workspace App
            </a>
            <h1 class="text-sm font-bold text-gray-900 dark:text-white">Pembayaran QRIS Pakasir</h1>
            <span class="w-12"></span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="mx-auto max-w-md space-y-4 px-4 pt-4">
        <!-- QRIS Display Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-theme-md dark:border-gray-800 dark:bg-gray-900">
            <div class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                Metode Pembayaran QRIS Instant
            </div>

            <div class="mt-4 rounded-xl border border-gray-100 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-800/60">
                @if ($paymentQrCode)
                    <img src="{{ $paymentQrCode }}" alt="QRIS Pakasir" class="mx-auto aspect-square w-full max-w-56 object-contain">
                @elseif ($qrisImage)
                    <img src="{{ $qrisImage }}" alt="QRIS Pakasir" class="mx-auto aspect-square w-full max-w-56 object-contain">
                @else
                    <div class="py-8 text-center text-xs text-gray-400">
                        Memuat QRIS Pakasir...
                    </div>
                @endif
            </div>

            <p class="mt-3 text-xs font-medium text-gray-600 dark:text-gray-300">
                Scan Kode QRIS di atas menggunakan <strong>GoPay, OVO, Dana, ShopeePay</strong>, atau <strong>Mobile Banking</strong> Anda.
            </p>
        </div>

        <!-- Payment Details Summary -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-3">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Rincian Pembayaran</h3>
            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                    <span class="text-gray-500 dark:text-gray-400">Paket Langganan:</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ str($submission->package)->headline() }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                    <span class="text-gray-500 dark:text-gray-400">Periode Tagihan:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ ($submission->billing_period ?? 'monthly') === 'yearly' ? 'Tahunan (-10%)' : 'Bulanan' }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                    <span class="text-gray-500 dark:text-gray-400">Total Pembayaran:</span>
                    <span class="text-sm font-extrabold text-brand-600 dark:text-brand-400"><x-money :amount="$submission->amount" /></span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                    <span class="text-gray-500 dark:text-gray-400">Status Pembayaran:</span>
                    <x-status-badge :status="$submission->status" />
                </div>
                @if($submission->payment_order_id)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400">ID Pesanan Pakasir:</span>
                        <span class="font-mono text-gray-800 dark:text-gray-200">{{ $submission->payment_order_id }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Mobile Action Buttons -->
        <div class="space-y-2.5">
            @if ($submission->payment_url)
                <a href="{{ $submission->payment_url }}" target="_blank" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-500 text-xs font-bold text-white shadow-theme-xs transition hover:bg-brand-600 active:scale-[0.98]">
                    Buka Link Pembayaran Pakasir ↗
                </a>
            @endif

            <a href="{{ route('mobile.app') }}" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                ← Kembali ke Workspace Mobile
            </a>
        </div>
    </main>
</div>
@endsection

@extends('layouts.app')

@section('content')
@php($isSuccess = $status === 'success')

<div class="mx-auto flex min-h-[60vh] max-w-xl items-center justify-center py-8">
    <section class="w-full rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full {{ $isSuccess ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' }}">
            @if ($isSuccess)
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>
            @else
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
            @endif
        </div>

        <h1 class="mt-5 text-xl font-semibold text-gray-900 dark:text-white/90">
            {{ $isSuccess ? 'Pembayaran Berhasil' : 'Pembayaran Dibatalkan' }}
        </h1>
        <p class="mx-auto mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400">
            {{ $isSuccess
                ? 'Pembayaran Anda telah diterima. Status langganan akan diperbarui otomatis setelah pembayaran terverifikasi.'
                : 'Pembayaran belum diselesaikan. Anda dapat kembali ke halaman billing untuk mencoba lagi.' }}
        </p>

        <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50/70 p-4 text-left text-sm dark:border-gray-800 dark:bg-gray-900/50">
            <div class="flex items-center justify-between gap-4">
                <span class="text-gray-500 dark:text-gray-400">Paket</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ str($submission->package)->headline() }}</span>
            </div>
            <div class="mt-2 flex items-center justify-between gap-4">
                <span class="text-gray-500 dark:text-gray-400">Status verifikasi</span>
                <span class="font-semibold {{ $submission->status === 'confirmed' ? 'text-success-600 dark:text-success-400' : 'text-amber-600 dark:text-amber-400' }}">
                    {{ $submission->status === 'confirmed' ? 'Terkonfirmasi' : 'Menunggu' }}
                </span>
            </div>
        </div>

        <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('settings.billing') }}" class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-600">Kembali ke Billing</a>
            @if ($isSuccess && $submission->status !== 'confirmed')
                <a href="{{ route('settings.billing.show', $submission) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800">Lihat Status Pembayaran</a>
            @endif
        </div>
    </section>
</div>
@endsection

@extends('layouts.guest')

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-4">
    @if(in_array($invoice->status, ['sent', 'partial']))
        <div class="rounded-xl border border-brand-200 bg-brand-50 p-4 shadow-sm sm:flex sm:items-center sm:justify-between dark:border-brand-900/50 dark:bg-brand-900/20">
            <div>
                <h3 class="text-sm font-semibold text-brand-900 dark:text-brand-100">Tagihan Belum Lunas</h3>
                <p class="mt-1 text-xs text-brand-700 dark:text-brand-300">
                    Sisa tagihan sebesar <span class="font-bold"><x-money :amount="$invoice->balance_due" /></span>. Pembayaran dapat dilakukan secara online melalui QRIS atau Virtual Account.
                </p>
            </div>
            <div class="mt-4 sm:ml-4 sm:mt-0 sm:shrink-0">
                <form action="{{ route('public.invoices.pay', $invoice->public_token) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                        Bayar Sekarang
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-md bg-error-50 p-4 text-sm text-error-800">
            {{ session('error') }}
        </div>
    @endif

    <x-document.preview :document="$invoice" />
</div>
@endsection

@extends('layouts.fullscreen-layout')

@section('content')
<div x-data="{ showPaymentModal: false, showStatusModal: false }" class="min-h-screen bg-[#F7F6F3] pb-28 text-gray-900 dark:bg-gray-950 dark:text-white">
    <!-- Top Header App Bar -->
    <header class="sticky top-0 z-40 border-b border-gray-200/80 bg-white/95 px-4 py-4 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-900/95">
        <div class="mx-auto flex max-w-md items-center justify-between">
            <a href="{{ route('mobile.app') }}" aria-label="Kembali" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <x-heroicon-o-chevron-left class="h-5 w-5" />
            </a>
            <div class="text-center">
                <h1 class="text-xs font-bold text-gray-900 dark:text-white">{{ $invoice->number }}</h1>
                <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $invoice->client?->name }}</p>
            </div>
            <div class="flex items-center">
                <x-status-badge :status="$invoice->status" />
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="mx-auto max-w-md space-y-4 px-4 pt-4">


        <!-- Quick Action Buttons (Unduh PDF & Catat Bayar) -->
        <div class="grid grid-cols-2 gap-2.5 text-center">
            <a href="{{ route('invoices.pdf', $invoice) }}" class="flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 py-2.5 px-3 text-sky-700 shadow-theme-xs transition hover:bg-sky-100 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300 active:scale-95">
                <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                <span class="text-xs font-bold">Unduh PDF</span>
            </a>

            <button @click="showPaymentModal = true" class="flex items-center justify-center gap-2 rounded-xl border border-brand-200 bg-brand-50 py-2.5 px-3 text-brand-700 shadow-theme-xs transition hover:bg-brand-100 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-400 active:scale-95">
                <x-heroicon-o-wallet class="h-4 w-4" />
                <span class="text-xs font-bold">+ Catat Bayar</span>
            </button>
        </div>

        <!-- Hero Finance Summary Card -->
        <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-blue-700 p-5 text-white shadow-theme-md">
            <div class="flex items-center justify-between text-xs font-medium text-white/80">
                <span>Total Invoice</span>
                <span class="font-mono text-[11px] text-white/90">ID: {{ $invoice->number }}</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-white">
                Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2 border-t border-white/15 pt-3 text-xs">
                <div>
                    <span class="text-[11px] text-white/75">Sudah Dibayar</span>
                    <p class="font-semibold text-emerald-200">Rp {{ number_format((float) $invoice->payments->sum('amount'), 0, ',', '.') }}</p>
                </div>
                <div class="border-l border-white/15 pl-3">
                    <span class="text-[11px] text-white/75">Sisa Piutang</span>
                    <p class="font-bold text-amber-200">Rp {{ number_format((float) $invoice->balance_due, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Client & Info Details -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-3 text-xs">
            <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-[11px]">Informasi Penerima & Tanggal</h3>
            <div class="grid grid-cols-2 gap-3 border-b border-gray-100 pb-3 dark:border-gray-800">
                <div>
                    <span class="text-gray-400 text-[10px] uppercase font-semibold">Ditagihkan Kepada</span>
                    <p class="font-bold text-gray-900 dark:text-white mt-0.5">{{ $invoice->client?->name }}</p>
                    @if($invoice->client?->company_name)
                        <p class="text-gray-500 dark:text-gray-400">{{ $invoice->client->company_name }}</p>
                    @endif
                    @if($invoice->client?->phone)
                        <p class="text-gray-500 dark:text-gray-400 mt-0.5">{{ $invoice->client->phone }}</p>
                    @endif
                </div>
                <div>
                    <span class="text-gray-400 text-[10px] uppercase font-semibold">Diterbitkan Oleh</span>
                    <p class="font-bold text-gray-900 dark:text-white mt-0.5">{{ $invoice->company?->name }}</p>
                    <p class="text-gray-500 dark:text-gray-400">{{ $invoice->company?->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <div>
                    <span class="text-gray-400 text-[10px]">Tanggal Terbit:</span>
                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $invoice->issue_date?->format('d M Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-400 text-[10px]">Jatuh Tempo:</span>
                    <p class="font-semibold text-amber-600 dark:text-amber-400">{{ $invoice->due_date?->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Items Breakdown List -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-3">
            <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-[11px]">Rincian Produk / Layanan</h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-800 space-y-3">
                @foreach ($invoice->items as $item)
                    @php
                        $lines = array_filter(array_map('trim', explode('-', $item->description)));
                    @endphp
                    <div class="pt-3 first:pt-0 space-y-1 text-xs">
                        @foreach($lines as $index => $line)
                            <p class="font-semibold text-gray-900 dark:text-white">
                                @if($index > 0)- @endif{{ $line }}
                            </p>
                        @endforeach
                        <p class="text-gray-400 text-[11px] pt-0.5">Qty {{ number_format((float) $item->quantity, 0, ',', '.') }} x Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Tax & Totals Breakdown -->
            <div class="border-t border-gray-100 pt-3 space-y-1 text-xs dark:border-gray-800">
                <div class="flex justify-between text-gray-500 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                @foreach ($invoice->normalized_custom_taxes as $tax)
                    @if ($tax['rate'] > 0 || $tax['amount'] > 0)
                        <div class="flex justify-between text-gray-500 dark:text-gray-400">
                            <span>{{ $tax['name'] }} ({{ $tax['rate'] }}%)</span>
                            <span class="{{ $tax['type'] === 'deduction' ? 'text-rose-600 dark:text-rose-400' : '' }}">
                                @if ($tax['type'] === 'deduction')
                                    (Rp {{ number_format((float) $tax['amount'], 0, ',', '.') }})
                                @else
                                    Rp {{ number_format((float) $tax['amount'], 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                    @endif
                @endforeach
                <div class="flex justify-between font-extrabold text-sm text-gray-900 dark:text-white pt-2 border-t border-gray-100 dark:border-gray-800">
                    <span>Total Invoice</span>
                    <span class="text-brand-600 dark:text-brand-400">Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment History List -->
        @if($invoice->payments->isNotEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-3 text-xs">
                <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-[11px]">Riwayat Pembayaran Diterima</h3>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($invoice->payments as $payment)
                        <div class="py-2 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                                <p class="text-[11px] text-gray-400">{{ $payment->payment_method ?? 'Transfer' }} • {{ $payment->paid_at?->format('d M Y') }}</p>
                            </div>
                            @if($payment->proof_path)
                                <a href="{{ Storage::url($payment->proof_path) }}" target="_blank" class="rounded border border-gray-200 bg-gray-50 px-2 py-1 text-[10px] font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    Bukti
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </main>

    <!-- STICKY BOTTOM ACTION BAR (BOXICONS SVG) -->
    <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-200/80 bg-white/95 px-4 py-3 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-900/95">
        <div class="mx-auto flex max-w-md items-center gap-3">
            <a href="https://wa.me/?text={{ urlencode('Halo '.$invoice->client?->name.', berikut link invoice Anda ('.$invoice->number.'): '.route('public.invoices.show', $invoice->public_token)) }}" target="_blank" class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-xs font-bold text-white shadow-theme-xs transition hover:bg-emerald-700 active:scale-[0.98]">
                <x-heroicon-o-share class="h-5 w-5" />
                <span>Bagikan WA</span>
            </a>

            <button @click="showStatusModal = true" class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-xs font-bold text-white shadow-theme-xs transition hover:bg-amber-600 active:scale-[0.98]">
                <x-heroicon-o-pencil-square class="h-5 w-5" />
                <span>Ubah Status</span>
            </button>
        </div>
    </div>

    <!-- MOBILE STATUS UPDATE MODAL -->
    <div
        x-show="showStatusModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 backdrop-blur-xs sm:items-center"
        style="display: none;"
    >
        <div
            x-show="showStatusModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            class="flex max-h-[75vh] w-full max-w-md flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900 sm:rounded-2xl"
        >
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ubah Status Invoice</h3>
                <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Tutup modal">
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>

            <form method="POST" action="{{ route('invoices.status', $invoice) }}" class="flex flex-1 flex-col overflow-hidden">
                @csrf
                @method('PATCH')
                <input type="hidden" name="from_mobile" value="1">
                <div class="flex-1 space-y-3 overflow-y-auto p-5 text-xs">
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Pilih Status Baru</label>
                        <select name="status" class="w-full rounded-lg border border-gray-200 bg-transparent py-2.5 px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                            <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent (Terkirim)</option>
                            <option value="partial" {{ $invoice->status === 'partial' ? 'selected' : '' }}>Partial (Dibayar Sebagian)</option>
                            <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                            <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Overdue (Jatuh Tempo)</option>
                            <option value="void" {{ $invoice->status === 'void' ? 'selected' : '' }}>Void (Dibatalkan)</option>
                        </select>
                    </div>
                </div>

                <div class="shrink-0 border-t border-gray-100 bg-white/95 p-4 backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                        Simpan Perubahan Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MOBILE PAYMENT RECORD MODAL -->
    <div
        x-show="showPaymentModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 backdrop-blur-xs sm:items-center"
        style="display: none;"
    >
        <div
            x-show="showPaymentModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            class="flex max-h-[75vh] w-full max-w-md flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900 sm:rounded-2xl"
        >
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Catat Pembayaran Invoice</h3>
                <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Tutup modal">
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>

            <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" enctype="multipart/form-data" class="flex flex-1 flex-col overflow-hidden">
                @csrf
                <input type="hidden" name="from_mobile" value="1">
                <div class="flex-1 space-y-3 overflow-y-auto p-5 text-xs">
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Jumlah Pembayaran (Rp)</label>
                        <input type="number" step="0.01" name="amount" value="{{ $invoice->balance_due }}" required class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Tanggal Bayar</label>
                            <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Metode Bayar</label>
                            <select name="payment_method" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Tunai / Cash">Tunai / Cash</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Unggah Bukti Transaksi (Opsional)</label>
                        <input type="file" name="proof_file" accept="image/*,.pdf" class="w-full text-xs text-gray-500">
                    </div>
                </div>

                <div class="shrink-0 border-t border-gray-100 bg-white/95 p-4 backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

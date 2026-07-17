@extends('layouts.app')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-2">
    <a href="{{ route('invoices.pdf', $invoice) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Download PDF</a>
    
    <!-- Share Button (Dropdown/Popover via Alpine.js) -->
    <div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
        <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 cursor-pointer">
            <i class="bx bx-share-alt text-gray-500 dark:text-gray-400 text-base"></i>
            <span>Bagikan</span>
            <i class="bx bx-chevron-down text-gray-400 text-sm"></i>
        </button>
        
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100" 
             x-transition:enter-start="transform opacity-0 scale-95" 
             x-transition:enter-end="transform opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-75" 
             x-transition:leave-start="transform opacity-100 scale-100" 
             x-transition:leave-end="transform opacity-0 scale-95" 
             class="absolute left-0 mt-2 w-52 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none dark:bg-gray-800 dark:ring-white/5 z-50"
             style="display: none;">
            <div class="py-1" role="menu">
                <!-- Salin Tautan -->
                <button @click="
                    navigator.clipboard.writeText('{{ route('public.invoices.show', $invoice->public_token) }}');
                    window.toast('success', 'Tautan disalin ke clipboard!');
                    open = false;
                " class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 cursor-pointer" role="menuitem">
                    <i class="bx bx-link text-gray-400 text-base mr-1"></i>
                    Salin Tautan
                </button>
                
                <!-- Buka Tautan Publik -->
                <a href="{{ route('public.invoices.show', $invoice->public_token) }}" 
                   target="_blank"
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <i class="bx bx-link-external text-gray-400 text-base mr-1"></i>
                    Buka Tautan Publik
                </a>
                
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text={{ rawurlencode('Halo, berikut tagihan Invoice Anda dari ' . $invoice->company->name . ' dengan nomor ' . $invoice->number . ': ' . route('public.invoices.show', $invoice->public_token)) }}" 
                   target="_blank" 
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <i class="bx bxl-whatsapp text-success-600 text-base mr-1"></i>
                    WhatsApp
                </a>
                
                <!-- Email -->
                <a href="mailto:?subject={{ rawurlencode('Invoice Tagihan ' . $invoice->number) }}&body={{ rawurlencode('Halo, berikut adalah tagihan Invoice Anda dari ' . $invoice->company->name . ' dengan nomor ' . $invoice->number . '. Silakan akses melalui tautan berikut: ' . route('public.invoices.show', $invoice->public_token)) }}" 
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <i class="bx bx-envelope text-blue-600 text-base mr-1"></i>
                    Email
                </a>
                
                <!-- Telegram -->
                <a href="https://t.me/share/url?url={{ rawurlencode(route('public.invoices.show', $invoice->public_token)) }}&text={{ rawurlencode('Halo, berikut tagihan Invoice Anda dari ' . $invoice->company->name . ' dengan nomor ' . $invoice->number) }}" 
                   target="_blank" 
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <i class="bx bxl-telegram text-sky-500 text-base mr-1"></i>
                    Telegram
                </a>
            </div>
        </div>
    </div>

    @if($invoice->status === 'draft')
        <form method="POST" action="{{ route('invoices.send', $invoice) }}">
            @csrf
            <button class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white cursor-pointer">Send to Client</button>
        </form>
    @endif
</div>
@include('invoices._status_flow', ['invoice' => $invoice])

<div class="grid gap-6 lg:grid-cols-[1fr_24rem] mt-6">
    <div class="space-y-6">
        <x-document.preview :document="$invoice" />
        @include('invoices.partials.expenses', ['invoice' => $invoice])
        @include('invoices.partials.credit-notes', ['invoice' => $invoice])
    </div>
    <aside class="space-y-4">
        <x-modal class="p-5">
            <!-- Header -->
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3 dark:border-gray-800">
                <i class="bx bx-receipt text-brand-600 dark:text-brand-400 text-lg"></i>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Buku Kas & Catatan Pembayaran</h2>
            </div>

            <!-- Progress / Visual Indicator -->
            @php
                $total = (float) $invoice->total;
                $paid = (float) $invoice->amount_paid;
                $percent = $total > 0 ? min(round(($paid / $total) * 100), 100) : 0;
            @endphp
            <div class="mt-4">
                <div class="flex justify-between items-center text-xs font-medium mb-1">
                    <span class="text-gray-500">Progress Pelunasan</span>
                    <span class="text-brand-700 dark:text-brand-400 font-semibold">{{ $percent }}% Terbayar</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                    <div class="bg-brand-500 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                </div>
            </div>

            <!-- Ledger Details -->
            <div class="mt-4 space-y-2 text-xs leading-relaxed">
                <div class="flex justify-between items-center py-1">
                    <span class="text-gray-500">Total Tagihan</span>
                    <span class="font-semibold text-gray-900 dark:text-white"><x-money :amount="$invoice->total" /></span>
                </div>

                @if((float) $invoice->down_payment_amount > 0)
                    <div class="flex justify-between items-center py-1 border-t border-gray-50 dark:border-gray-800/40">
                        <span class="text-gray-500">Uang Muka (DP)</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200"><x-money :amount="$invoice->down_payment_amount" /></span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-gray-400 pl-3">
                        <span>DP Terbayar</span>
                        <span><x-money :amount="$invoice->down_payment_paid" /></span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-gray-400 pl-3">
                        <span>Sisa Uang Muka</span>
                        <span><x-money :amount="$invoice->down_payment_remaining" /></span>
                    </div>
                @endif

                <div class="flex justify-between items-center py-1 border-t border-gray-50 dark:border-gray-800/40">
                    <span class="text-gray-500">Total Terbayar</span>
                    <span class="font-semibold text-success-600 dark:text-success-400"><x-money :amount="$invoice->amount_paid" /></span>
                </div>

                @if((float) $invoice->credit_note_total > 0)
                    <div class="flex justify-between items-center py-1 border-t border-gray-50 dark:border-gray-800/40 text-brand-700 dark:text-brand-400">
                        <span>Nota Kredit (Credit Note)</span>
                        <span class="font-medium"><x-money :amount="$invoice->credit_note_total" /></span>
                    </div>
                @endif

                <!-- Balance Due (Highlight) -->
                <div class="flex justify-between items-center py-2 border-t border-gray-200 pt-2 dark:border-gray-800">
                    <span class="font-medium text-gray-800 dark:text-gray-200">Sisa Tagihan (Piutang)</span>
                    <span class="font-bold text-sm 
                        @if((float) $invoice->balance_due > 0) text-amber-600 dark:text-amber-400 @else text-success-600 dark:text-success-400 @endif
                    ">
                        <x-money :amount="$invoice->balance_due" />
                    </span>
                </div>

                <!-- Profit & Loss -->
                @if((float) $invoice->expense_total > 0)
                    <div class="mt-4 rounded-lg bg-gray-50 p-3 dark:bg-white/[0.02] border border-gray-100 dark:border-gray-800/50 space-y-1.5 text-[11px]">
                        <div class="flex justify-between text-gray-400">
                            <span>Total Pengeluaran Projek</span>
                            <span><x-money :amount="$invoice->expense_total" /></span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Potongan PPh Terutang</span>
                            <span><x-money :amount="$invoice->pph_amount" /></span>
                        </div>
                        <div class="flex justify-between font-semibold text-gray-800 dark:text-gray-200 border-t border-gray-200/50 pt-1.5 dark:border-gray-800">
                            <span>Keuntungan Bersih (Profit)</span>
                            <span class="text-brand-600 dark:text-brand-400"><x-money :amount="$invoice->profit_total" /></span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Form Catat Pembayaran -->
            @if((float) $invoice->balance_due > 0)
                <div x-data="{ showForm: false }" class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <button @click="showForm = !showForm" type="button" class="flex w-full items-center justify-between text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 cursor-pointer">
                        <span>Catat Pembayaran Baru</span>
                        <i class="bx bx-plus text-xs" x-show="!showForm"></i>
                        <i class="bx bx-minus text-xs" x-show="showForm" style="display: none;"></i>
                    </button>

                    <form x-show="showForm" 
                          x-transition
                          method="POST" 
                          action="{{ route('invoices.payments.store', $invoice) }}" 
                          class="mt-4 space-y-3" 
                          enctype="multipart/form-data"
                          style="display: none;">
                        @csrf
                        <x-form.input name="amount" label="Jumlah Pembayaran" type="number" step="0.01" :value="$invoice->balance_due" />
                        <x-form.input name="paid_at" label="Tanggal Pembayaran" type="date" :value="now()->toDateString()" />
                        
                        <x-form.select name="method" label="Metode Pembayaran">
                            <option value="bank_transfer" selected>Transfer Bank</option>
                            <option value="cash">Tunai / Cash</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="e_wallet">E-Wallet (GoPay, OVO, ShopeePay, dll)</option>
                            <option value="other">Lainnya</option>
                        </x-form.select>

                        <x-form.input name="reference" label="No. Referensi / Transaksi" />
                        
                        <button class="w-full rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white hover:bg-gray-800 transition-colors cursor-pointer">
                            Simpan Pembayaran
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-5 border-t border-gray-100 pt-3 dark:border-gray-800 text-center text-xs text-success-600 dark:text-success-400 font-semibold flex items-center justify-center gap-1.5">
                    <i class="bx bx-check-circle text-base"></i>
                    Tagihan Telah Lunas Sepenuhnya
                </div>
            @endif
        </x-modal>
    </aside>
</div>
@endsection

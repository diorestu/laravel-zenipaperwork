@extends('layouts.app')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-2">
    <a href="{{ route('invoices.pdf', $invoice) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Unduh PDF</a>

    @if ($invoice->invoice_type === 'down_payment')
        <a href="{{ route('invoices.index', ['modal' => 'create', 'invoice_type' => 'settlement', 'parent_invoice_id' => $invoice->id, 'client_id' => $invoice->client_id]) }}" class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            ⚡ Buat Invoice Pelunasan
        </a>
    @endif
    
    <!-- Share Button (Dropdown/Popover via Alpine.js) -->
    <div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
        <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 cursor-pointer">
            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8.5 13.5 15.5 17.5M15.5 6.5 8.5 10.5M18 8.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM6 14.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM18 20.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Bagikan</span>
            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                    <svg class="mr-1 h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10.5 13.5a4 4 0 0 0 5.66 0l2.34-2.34a4 4 0 0 0-5.66-5.66l-1.05 1.05M13.5 10.5a4 4 0 0 0-5.66 0L5.5 12.84a4 4 0 0 0 5.66 5.66l1.05-1.05" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Salin Tautan
                </button>
                
                <!-- Buka Tautan Publik -->
                <a href="{{ route('public.invoices.show', $invoice->public_token) }}" 
                   target="_blank"
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <svg class="mr-1 h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 5h5v5M19 5l-8 8M19 14v3.5A1.5 1.5 0 0 1 17.5 19h-11A1.5 1.5 0 0 1 5 17.5v-11A1.5 1.5 0 0 1 6.5 5H10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Buka Tautan Publik
                </a>
                
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text={{ rawurlencode('Halo, berikut tagihan Invoice Anda dari ' . $invoice->company->name . ' dengan nomor ' . $invoice->number . ': ' . route('public.invoices.show', $invoice->public_token)) }}" 
                   target="_blank" 
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <svg class="mr-1 h-4 w-4 text-success-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5.6 18.4A8.5 8.5 0 1 1 8.1 20L4 21l1.6-2.6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.2 8.8c.2-.4.4-.4.7-.4h.5c.2 0 .4.1.5.4l.5 1.1c.1.3.1.5-.1.7l-.4.5c.6 1.1 1.5 2 2.6 2.6l.5-.4c.2-.2.5-.2.7-.1l1.1.5c.3.1.4.3.4.6v.4c0 .4-.1.6-.4.8-.4.3-1 .5-1.7.4-2.8-.4-5.4-3-5.8-5.8-.1-.7.1-1.3.4-1.7Z" fill="currentColor"/></svg>
                    WhatsApp
                </a>
                
                <!-- Email -->
                <a href="mailto:?subject={{ rawurlencode('Invoice Tagihan ' . $invoice->number) }}&body={{ rawurlencode('Halo, berikut adalah tagihan Invoice Anda dari ' . $invoice->company->name . ' dengan nomor ' . $invoice->number . '. Silakan akses melalui tautan berikut: ' . route('public.invoices.show', $invoice->public_token)) }}" 
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <svg class="mr-1 h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5.5 6h13A1.5 1.5 0 0 1 20 7.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 16.5v-9A1.5 1.5 0 0 1 5.5 6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m5 7 7 6 7-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Email
                </a>
                
                <!-- Telegram -->
                <a href="https://t.me/share/url?url={{ rawurlencode(route('public.invoices.show', $invoice->public_token)) }}&text={{ rawurlencode('Halo, berikut tagihan Invoice Anda dari ' . $invoice->company->name . ' dengan nomor ' . $invoice->number) }}" 
                   target="_blank" 
                   @click="open = false"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" role="menuitem">
                    <svg class="mr-1 h-4 w-4 text-sky-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 5 4 11.5l5.8 2.1M20 5l-3 14-7.2-5.4M20 5 9.8 13.6M9.8 13.6 9 18l2.7-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Telegram
                </a>
            </div>
        </div>
    </div>

    @if($invoice->status === 'draft')
        <form method="POST" action="{{ route('invoices.send', $invoice) }}">
            @csrf
            <button class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-brand-600 dark:hover:bg-brand-500 transition-colors cursor-pointer">Kirim ke Klien</button>
        </form>
    @endif
</div>
@include('invoices._status_flow', ['invoice' => $invoice])

<div class="grid gap-6 lg:grid-cols-[1fr_24rem] mt-6">
    <div class="space-y-6">
        <x-document.preview :document="$invoice" />
    </div>
    <aside class="space-y-4">
        <x-modal class="p-5">
            <!-- Header -->
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3 dark:border-gray-800">
                <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h10v18l-2-1.2-2 1.2-2-1.2-2 1.2-2-1.2-2 1.2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
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
                    <span class="text-gray-500 dark:text-gray-400">Progress Pelunasan</span>
                    <span class="text-brand-700 dark:text-brand-400 font-semibold">{{ $percent }}% Terbayar</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                    <div class="bg-brand-500 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                </div>
            </div>

            <!-- Ledger Details -->
            <div class="mt-4 space-y-2 text-xs leading-relaxed">
                <div class="flex justify-between items-center py-1">
                    <span class="text-gray-500 dark:text-gray-400">Total Tagihan</span>
                    <span class="font-semibold text-gray-900 dark:text-white"><x-money :amount="$invoice->total" /></span>
                </div>

                @if((float) $invoice->down_payment_amount > 0)
                    <div class="flex justify-between items-center py-1 border-t border-gray-50 dark:border-gray-800/40">
                        <span class="text-gray-500 dark:text-gray-400">Uang Muka (DP)</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200"><x-money :amount="$invoice->down_payment_amount" /></span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-gray-400 dark:text-gray-500 pl-3">
                        <span>DP Terbayar</span>
                        <span><x-money :amount="$invoice->down_payment_paid" /></span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-gray-400 dark:text-gray-500 pl-3">
                        <span>Sisa Uang Muka</span>
                        <span><x-money :amount="$invoice->down_payment_remaining" /></span>
                    </div>
                @endif

                <div class="flex justify-between items-center py-1 border-t border-gray-50 dark:border-gray-800/40">
                    <span class="text-gray-500 dark:text-gray-400">Total Terbayar</span>
                    <span class="font-semibold text-success-600 dark:text-success-400"><x-money :amount="$invoice->amount_paid" /></span>
                </div>

                @if((float) $invoice->credit_note_total > 0)
                    <div class="flex justify-between items-center py-1 border-t border-gray-50 dark:border-gray-800/40 text-brand-700 dark:text-brand-400">
                        <span>Nota Kredit</span>
                        <span class="font-medium"><x-money :amount="$invoice->credit_note_total" /></span>
                    </div>
                @endif

                <!-- Balance Jatuh tempo (Highlight) -->
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

            @if($invoice->paymentTerms->isNotEmpty())
                <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-gray-200">Jadwal Termin</h3>
                    <div class="mt-3 space-y-2">
                        @foreach($invoice->paymentTerms as $term)
                            @php
                                $paidForTerm = (float) $invoice->payments->where('term_number', $term->term_number)->sum('amount');
                                $remainingForTerm = max((float) $term->amount - $paidForTerm, 0);
                            @endphp
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 dark:border-gray-800 dark:bg-white/[0.02]">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $term->label }}</p>
                                        <p class="mt-0.5 text-[11px] text-gray-400">{{ $term->due_date ? 'Jatuh tempo '.$term->due_date->format('d M Y') : 'Tanpa jatuh tempo' }}</p>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white"><x-money :amount="$term->amount" /></span>
                                </div>
                                <div class="mt-1 flex justify-between text-[11px] text-gray-400">
                                    <span>Terbayar: <x-money :amount="$paidForTerm" /></span>
                                    <span>Sisa: <x-money :amount="$remainingForTerm" /></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($invoice->payments->isNotEmpty())
                <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-gray-200">Riwayat Pembayaran</h3>
                    <div class="mt-3 space-y-2">
                        @foreach($invoice->payments->sortByDesc('paid_at') as $payment)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 dark:border-gray-800 dark:bg-white/[0.02]">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $payment->term_label ?: 'Pembayaran manual' }}</p>
                                        <p class="mt-0.5 text-[11px] text-gray-400">{{ $payment->paid_at?->format('d M Y') }} · {{ str($payment->method)->headline() }}</p>
                                    </div>
                                    <span class="text-xs font-semibold text-success-600 dark:text-success-400"><x-money :amount="$payment->amount" /></span>
                                </div>
                                @if($payment->reference)
                                    <p class="mt-1 text-[11px] text-gray-400">Ref: {{ $payment->reference }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form Catat Pembayaran -->
            @if((float) $invoice->balance_due > 0)
                @php
                    $paymentTerms = $invoice->paymentTerms
                        ->map(function ($term) use ($invoice): array {
                            $paidForTerm = (float) $invoice->payments
                                ->where('term_number', $term->term_number)
                                ->sum('amount');
                            $remaining = max((float) $term->amount - $paidForTerm, 0);

                            return [
                                'number' => $term->term_number,
                                'label' => $term->label,
                                'amount' => $remaining,
                            ];
                        })
                        ->filter(fn (array $term): bool => $term['amount'] > 0)
                        ->values();
                @endphp
                <div
                    x-data="{
                        showForm: false,
                        terms: @js($paymentTerms->values()),
                        selectedTerm: @js((string) ($paymentTerms->first()['number'] ?? '')),
                        amount: @js((float) ($paymentTerms->first()['amount'] ?? $invoice->balance_due)),
                        get hasTerms() {
                            return this.terms.length > 0;
                        },
                        get selectedTermData() {
                            return this.terms.find((term) => String(term.number) === String(this.selectedTerm));
                        },
                        syncTerm() {
                            const term = this.selectedTermData;
                            if (!term) return;
                            this.amount = Number(term.amount).toFixed(2);
                        }
                    }"
                    class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <button @click="showForm = !showForm" type="button" class="flex w-full items-center justify-between text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 cursor-pointer">
                        <span>Catat Pembayaran Baru</span>
                        <svg class="h-4 w-4" x-show="!showForm" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <svg class="h-4 w-4" x-show="showForm" style="display: none;" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>

                    <form x-show="showForm" 
                          x-transition
                          method="POST" 
                          action="{{ route('invoices.payments.store', $invoice) }}" 
                          class="mt-4 space-y-3" 
                          enctype="multipart/form-data"
                          style="display: none;">
                        @csrf
                        <template x-if="hasTerms">
                            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                                <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Termin</span>
                                <span class="block">
                                    <select
                                        name="term_number"
                                        x-model="selectedTerm"
                                        @change="syncTerm()"
                                        class="w-full appearance-none rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.5%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat">
                                        <template x-for="term in terms" :key="term.number">
                                            <option :value="term.number" x-text="`${term.label} - Rp ${Number(term.amount).toLocaleString('id-ID')}`"></option>
                                        </template>
                                    </select>
                                    <input type="hidden" name="term_label" :value="selectedTermData?.label || ''">
                                    <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">Nominal otomatis mengikuti sisa termin yang dipilih.</span>
                                    @error('term_number')<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
                                </span>
                            </label>
                        </template>

                        <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                            <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Pembayaran</span>
                            <span class="block">
                                <input
                                    name="amount"
                                    type="number"
                                    step="0.01"
                                    x-model="amount"
                                    :readonly="hasTerms"
                                    placeholder="Jumlah Pembayaran"
                                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none read-only:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-gray-500 dark:read-only:bg-white/[0.03]">
                                @error('amount')<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
                            </span>
                        </label>
                        <x-form.input name="paid_at" label="Tanggal Pembayaran" type="date" :value="now()->toDateString()" />
                        
                        <x-form.select name="method" label="Metode Pembayaran">
                            <option value="bank_transfer" selected>Transfer Bank</option>
                            <option value="cash">Tunai / Cash</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="e_wallet">E-Wallet (GoPay, OVO, ShopeePay, dll)</option>
                            <option value="other">Lainnya</option>
                        </x-form.select>

                        <x-form.input name="reference" label="No. Referensi / Transaksi" />
                        
                        <button class="w-full rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white hover:bg-gray-800 dark:bg-brand-600 dark:hover:bg-brand-500 transition-colors cursor-pointer">
                            Simpan Pembayaran
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-5 border-t border-gray-100 pt-3 dark:border-gray-800 text-center text-xs text-success-600 dark:text-success-400 font-semibold flex items-center justify-center gap-1.5">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Tagihan Telah Lunas Sepenuhnya
                </div>
            @endif
        </x-modal>
    </aside>
</div>
@endsection

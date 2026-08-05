@extends('layouts.app')

@section('content')
<div x-data="{
    open: false,
    invoice: {
        id: null,
        number: '',
        client_name: '',
        client_company: '',
        total: 0,
        paid: 0,
        balance: 0,
        due_date: '',
        issue_date: '',
        status: '',
        is_overdue: false,
        status_label: '',
        status_color: '',
        notes: '',
        pdf_url: '#',
        edit_url: '#',
        show_url: '#'
    },
    formatCurrency(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    },
    formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    },
    openInvoiceDetail(inv) {
        this.invoice = inv;
        this.open = true;
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white/90">Kalender Jatuh Tempo</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Pantau tanggal jatuh tempo tagihan invoice untuk menjaga stabilitas arus kas.
            </p>
        </div>
        <div class="flex items-center">
            <button type="button" id="sync-btn" onclick="syncData()" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white font-medium text-sm px-4 py-2.5 shadow-theme-xs transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50">
                <svg id="sync-icon" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Sinkronisasi Data</span>
            </button>
        </div>
    </div>

    <!-- Navigation Card -->
    <div class="flex items-center justify-between bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-theme-xs">
        <div class="flex items-center gap-3">
            <span class="text-xl font-bold text-gray-800 dark:text-white/95">
                {{ $currentDate->locale('id')->translatedFormat('F Y') }}
            </span>
        </div>
        <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-900 p-1 rounded-xl">
            <a href="{{ $prevMonthUrl }}" class="p-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition duration-150" title="Bulan Sebelumnya">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <a href="{{ $todayUrl }}" class="px-3 py-1.5 rounded-lg hover:bg-white dark:hover:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300 transition duration-150">
                Hari Ini
            </a>
            <a href="{{ $nextMonthUrl }}" class="p-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition duration-150" title="Bulan Berikutnya">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Calendar View Area -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs overflow-hidden">
        <!-- Weekday Headers -->
        <div class="grid grid-cols-7 text-center border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
            <div class="px-2 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Senin</div>
            <div class="px-2 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Selasa</div>
            <div class="px-2 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rabu</div>
            <div class="px-2 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kamis</div>
            <div class="px-2 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Jumat</div>
            <div class="px-2 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sabtu</div>
            <div class="px-2 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Minggu</div>
        </div>

        <!-- Calendar Days Grid -->
        <div class="grid grid-cols-7 gap-[1px] bg-gray-200 dark:bg-gray-800">
            @foreach ($days as $day)
                @php
                    $isCurrentMonth = $day['is_current_month'];
                    $isToday = $day['is_today'];
                    $hasInvoices = $day['invoices']->isNotEmpty();
                    
                    $cellClass = 'relative min-h-[145px] p-2.5 flex flex-col bg-white dark:bg-gray-950 transition duration-150';
                    if (!$isCurrentMonth) {
                        $cellClass .= ' bg-gray-50/50 dark:bg-gray-900/35 text-gray-400 dark:text-gray-600';
                    } else {
                        $cellClass .= ' hover:bg-gray-50/50 dark:hover:bg-gray-900/50';
                    }
                    
                    if ($isToday) {
                        $cellClass .= ' ring-2 ring-brand-500 ring-inset dark:ring-brand-400 z-10';
                    }
                @endphp
                
                <div class="{{ $cellClass }}">
                    <!-- Day Indicator -->
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold {{ $isToday ? 'bg-brand-500 text-white w-6 h-6 rounded-full flex items-center justify-center' : 'text-gray-600 dark:text-gray-400' }} {{ !$isCurrentMonth ? 'opacity-40' : '' }}">
                            {{ $day['day_number'] }}
                        </span>
                        @if ($hasInvoices)
                            <span class="text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full" title="{{ $day['invoices']->count() }} Invoice jatuh tempo">
                                {{ $day['invoices']->count() }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Invoice Blocks -->
                    <div class="flex-1 space-y-1.5 overflow-y-auto no-scrollbar max-h-[105px]">
                        @if ($hasInvoices)
                            @foreach ($day['invoices'] as $invoice)
                                @php
                                    $paid = (float) $invoice->payments->sum('amount');
                                    $total = (float) $invoice->total;
                                    $balance = max($total - $paid, 0);
                                    
                                    $status = $invoice->status;
                                    $isOverdue = $invoice->is_overdue;
                                    
                                    $statusLabel = match($status) {
                                        'draft' => 'Draft',
                                        'sent' => ($isOverdue ? 'Jatuh Tempo' : 'Terkirim'),
                                        'partial' => 'Sebagian',
                                        'paid' => 'Lunas',
                                        'void' => 'Batal',
                                        default => ucfirst($status),
                                    };
                                    
                                    $statusClass = match($status) {
                                        'paid' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/30',
                                        'partial' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/50 hover:bg-blue-100 dark:hover:bg-blue-900/30',
                                        'sent' => $isOverdue 
                                            ? 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50 hover:bg-rose-100 dark:hover:bg-rose-900/30'
                                            : 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900/50 hover:bg-amber-100 dark:hover:bg-amber-900/30',
                                        'draft' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700/50',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-900/50 dark:text-gray-400 dark:border-gray-850 hover:bg-gray-100 dark:hover:bg-gray-800',
                                    };
                                    
                                    $statusDotClass = match($status) {
                                        'paid' => 'bg-emerald-500',
                                        'partial' => 'bg-blue-500',
                                        'sent' => $isOverdue ? 'bg-rose-500' : 'bg-amber-500',
                                        'draft' => 'bg-gray-500',
                                        default => 'bg-gray-400',
                                    };
                                    
                                    $invoiceData = [
                                        'id' => $invoice->id,
                                        'number' => $invoice->number,
                                        'client_name' => $invoice->client->name,
                                        'client_company' => $invoice->client->company_name ?? '-',
                                        'total' => $total,
                                        'paid' => $paid,
                                        'balance' => $balance,
                                        'due_date' => $invoice->due_date?->toDateString(),
                                        'issue_date' => $invoice->issue_date?->toDateString(),
                                        'status' => $status,
                                        'is_overdue' => $isOverdue,
                                        'status_label' => $statusLabel,
                                        'status_color' => $statusDotClass,
                                        'notes' => $invoice->notes ?? '',
                                        'pdf_url' => route('invoices.pdf', $invoice->id),
                                        'edit_url' => route('invoices.edit', $invoice->id),
                                        'show_url' => route('invoices.show', $invoice->id),
                                    ];
                                @endphp
                                
                                <button 
                                    type="button"
                                    @click="openInvoiceDetail({{ json_encode($invoiceData) }})"
                                    class="w-full text-left truncate text-[11px] leading-4 py-1.5 px-2 rounded-lg border font-semibold flex items-center gap-1.5 shadow-theme-xs transition duration-200 hover:-translate-y-0.5 cursor-pointer {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDotClass }} shrink-0"></span>
                                    <span class="truncate font-semibold">{{ $invoice->number }}</span>
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Popup Modal Detail Invoice -->
    <div x-show="open" 
         x-cloak 
         class="fixed inset-0 z-99999 flex items-center justify-center p-4 overflow-y-auto"
         @keydown.escape.window="open = false">
         
        <!-- Backdrop -->
        <div x-show="open" 
             @click="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs"></div>
             
        <!-- Modal Content Box -->
        <div x-show="open"
             @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-theme-xl overflow-hidden p-6">
             
            <!-- Close button -->
            <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Header -->
            <div class="mb-5 pr-6">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Detail Invoice</span>
                    <span class="w-1.5 h-1.5 rounded-full" :class="invoice.status_color"></span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white" x-text="invoice.number"></h3>
            </div>
            
            <!-- Details Grid -->
            <div class="space-y-4 py-4 border-t border-b border-gray-100 dark:border-gray-800">
                <!-- Status -->
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Status</span>
                    <div class="col-span-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              :class="{
                                  'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400': invoice.status === 'paid',
                                  'bg-blue-50 text-blue-800 dark:bg-blue-950/40 dark:text-blue-400': invoice.status === 'partial',
                                  'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-400': invoice.status === 'sent' && !invoice.is_overdue,
                                  'bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-400': invoice.status === 'sent' && invoice.is_overdue,
                                  'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300': invoice.status === 'draft',
                                  'bg-gray-50 text-gray-600 dark:bg-gray-900/50 dark:text-gray-400': invoice.status === 'void'
                              }" x-text="invoice.status_label"></span>
                    </div>
                </div>
                
                <!-- Client Name -->
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pelanggan</span>
                    <div class="col-span-2">
                        <div class="text-sm font-bold text-gray-800 dark:text-white/90" x-text="invoice.client_name"></div>
                        <div class="text-xs text-gray-400 mt-0.5" x-text="invoice.client_company"></div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Tgl Terbit</span>
                    <div class="col-span-2 text-sm text-gray-700 dark:text-gray-300" x-text="formatDate(invoice.issue_date)"></div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Jatuh Tempo</span>
                    <div class="col-span-2 text-sm font-semibold" :class="invoice.is_overdue ? 'text-rose-600 dark:text-rose-400' : 'text-gray-700 dark:text-gray-300'" x-text="formatDate(invoice.due_date)"></div>
                </div>
                
                <!-- Amounts -->
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-dashed border-gray-100 dark:border-gray-800">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Tagihan</span>
                    <div class="col-span-2 text-sm font-bold text-gray-800 dark:text-white" x-text="formatCurrency(invoice.total)"></div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Terbayar</span>
                    <div class="col-span-2 text-sm font-bold text-emerald-600 dark:text-emerald-400" x-text="formatCurrency(invoice.paid)"></div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Sisa</span>
                    <div class="col-span-2 text-sm font-bold" :class="invoice.balance > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500'" x-text="formatCurrency(invoice.balance)"></div>
                </div>
                
                <!-- Notes -->
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-dashed border-gray-100 dark:border-gray-800" x-show="invoice.notes">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Catatan</span>
                    <div class="col-span-2 text-xs text-gray-600 dark:text-gray-400 italic bg-gray-50 dark:bg-gray-800/40 p-2 rounded-lg" x-text="invoice.notes"></div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row gap-2">
                <a :href="invoice.show_url" class="flex-1 inline-flex justify-center items-center rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 shadow-theme-xs transition-colors duration-150">
                    Detail Invoice
                </a>
                <a :href="invoice.pdf_url" target="_blank" class="inline-flex justify-center items-center rounded-xl border border-gray-350 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-semibold px-4 py-2.5 transition-colors duration-150">
                    Unduh PDF
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function syncData() {
        const btn = document.getElementById('sync-btn');
        const icon = document.getElementById('sync-icon');
        if (!btn || btn.disabled) return;

        btn.disabled = true;
        icon.classList.add('animate-spin-custom');

        fetch('{{ route("calendar.sync") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.toast) {
                    window.toast('success', data.message);
                }
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                if (window.toast) {
                    window.toast('error', data.message || 'Gagal menyinkronkan data.');
                }
                btn.disabled = false;
                icon.classList.remove('animate-spin-custom');
            }
        })
        .catch(error => {
            console.error('Error syncing:', error);
            if (window.toast) {
                window.toast('error', 'Terjadi kesalahan sistem saat sinkronisasi.');
            }
            btn.disabled = false;
            icon.classList.remove('animate-spin-custom');
        });
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .animate-spin-custom {
        animation: spin 1.2s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection

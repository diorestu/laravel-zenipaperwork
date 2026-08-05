@extends('layouts.fullscreen-layout')

@section('content')
@php
    $productJson = $products->where('is_active', true)->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'description' => $p->description ?? $p->name, 'price' => (float) $p->price, 'unit' => $p->unit])->values()->toJson();
    $defaultNotes = "Terima kasih atas kerja sama Anda.\n\n";
    if ($bankAccounts->isNotEmpty()) {
        $defaultNotes .= "Pembayaran dapat ditransfer melalui rekening berikut:\n";
        foreach ($bankAccounts as $acc) {
            $defaultNotes .= "- {$acc->bank_name} a/n {$acc->account_name} ({$acc->account_number})\n";
        }
        $defaultNotes .= "\n";
    }
    $defaultNotes .= "Konfirmasi pembayaran dapat dilakukan dalam jangka waktu maksimal 7 hari setelah tanggal invoice ini.";
@endphp

<div x-data="mobileAppWorkspace()" class="min-h-screen bg-[#F7F6F3] pb-24 text-gray-900 dark:bg-gray-950 dark:text-white">
    <!-- Top Header App Bar (Revamped & Taller) -->
    <header class="sticky top-0 z-40 border-b border-gray-200/80 bg-white/95 px-4 py-4 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-900/95">
        <div class="mx-auto flex max-w-md items-center justify-between">
            <div class="flex items-center gap-3">
                @if(auth()->user()->company?->logo_path)
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-theme-xs">
                        <img src="{{ asset('storage/' . auth()->user()->company->logo_path) }}" alt="{{ auth()->user()->company->name }}" class="h-full w-full object-cover">
                    </div>
                @else
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-base font-extrabold text-white shadow-theme-xs">
                        {{ strtoupper(substr(auth()->user()->company?->name ?? 'P', 0, 1)) }}
                    </div>
                @endif
                <div class="flex flex-col">
                    <h1 class="text-base font-bold tracking-tight text-gray-900 dark:text-white leading-tight">
                        {{ auth()->user()->company?->name ?? 'Paperwork' }}
                    </h1>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ auth()->user()->name }}
                    </span>
                </div>
            </div>

            <!-- Single Settings / Gear Action Button -->
            <button @click="openSettingsModal()" title="Pengaturan Aplikasi" class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-gray-50/80 text-gray-600 transition hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95">
                <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="mx-auto max-w-md px-4 pt-4 space-y-4">

        <!-- TAB 1: RINGKASAN (Home Revamp) -->
        <div x-show="activeTab === 'home'" class="space-y-4">
            <!-- Hero Finance Summary Card -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 via-brand-500 to-blue-700 p-5 text-white shadow-theme-md">
                <!-- Background Decorative Pattern -->
                <div class="absolute -right-6 -top-6 h-32 w-32 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-black/10 blur-xl pointer-events-none"></div>

                <div class="relative z-10">
                    <!-- Period Filter Row -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-xs font-medium text-white/80">Total Penjualan</span>
                        <div class="flex items-center gap-1.5">
                            <select x-model="filterMonth" @change="fetchStats()"
                                class="appearance-none rounded-lg bg-white/20 px-2 py-1 text-[11px] font-semibold text-white backdrop-blur-xs border border-white/20 outline-none cursor-pointer"
                                style="-webkit-appearance: none;">
                                <option value="1" class="text-gray-900">Januari</option>
                                <option value="2" class="text-gray-900">Februari</option>
                                <option value="3" class="text-gray-900">Maret</option>
                                <option value="4" class="text-gray-900">April</option>
                                <option value="5" class="text-gray-900">Mei</option>
                                <option value="6" class="text-gray-900">Juni</option>
                                <option value="7" class="text-gray-900">Juli</option>
                                <option value="8" class="text-gray-900">Agustus</option>
                                <option value="9" class="text-gray-900">September</option>
                                <option value="10" class="text-gray-900">Oktober</option>
                                <option value="11" class="text-gray-900">November</option>
                                <option value="12" class="text-gray-900">Desember</option>
                            </select>
                            <select x-model="filterYear" @change="fetchStats()"
                                class="appearance-none rounded-lg bg-white/20 px-2 py-1 text-[11px] font-semibold text-white backdrop-blur-xs border border-white/20 outline-none cursor-pointer"
                                style="-webkit-appearance: none;">
                                @for ($y = now()->year; $y >= now()->year - 3; $y--)
                                    <option value="{{ $y }}" class="text-gray-900">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Revenue Display -->
                    <div class="text-2xl font-bold tracking-tight text-white transition-opacity duration-300" :class="{ 'opacity-40': statsLoading }">
                        <span x-text="statsData.revenue_formatted">Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</span>
                    </div>

                    <!-- Inner Quick Stats Bar -->
                    <div class="mt-4 grid grid-cols-2 gap-2 border-t border-white/15 pt-3.5 text-xs transition-opacity duration-300" :class="{ 'opacity-40': statsLoading }">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-white/75">Piutang Aktif</span>
                            <span class="font-semibold text-white" x-text="statsData.unpaid_balance_formatted">Rp {{ number_format($stats['unpaid_balance'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/15 pl-3">
                            <span class="text-[11px] text-white/75">Overdue / Jatuh Tempo</span>
                            <span class="font-semibold text-amber-200" x-text="statsData.overdue_count + ' Invoice'">{{ $stats['overdue_count'] }} Invoice</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access Grid -->
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Quick Access</h2>
                <div class="mt-3.5 grid grid-cols-4 gap-2 text-center">
                    <button @click="openCreateInvoiceModal()" class="group flex flex-col items-center gap-1.5 rounded-xl p-2 transition hover:bg-gray-50 dark:hover:bg-white/[0.03] active:scale-95">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 shadow-theme-xs dark:bg-brand-500/10 dark:text-brand-400">
                            <x-heroicon-o-document-text class="h-5 w-5" />
                        </div>
                        <span class="text-[11px] font-medium text-gray-800 dark:text-gray-200">Invoice</span>
                    </button>
                    <button @click="openCreateQuotationModal()" class="group flex flex-col items-center gap-1.5 rounded-xl p-2 transition hover:bg-gray-50 dark:hover:bg-white/[0.03] active:scale-95">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 shadow-theme-xs dark:bg-violet-500/10 dark:text-violet-400">
                            <x-heroicon-o-document-plus class="h-5 w-5" />
                        </div>
                        <span class="text-[11px] font-medium text-gray-800 dark:text-gray-200">Penawaran</span>
                    </button>
                    <button @click="openCreateClientModal()" class="group flex flex-col items-center gap-1.5 rounded-xl p-2 transition hover:bg-gray-50 dark:hover:bg-white/[0.03] active:scale-95">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shadow-theme-xs dark:bg-emerald-500/10 dark:text-emerald-400">
                            <x-heroicon-o-user-plus class="h-5 w-5" />
                        </div>
                        <span class="text-[11px] font-medium text-gray-800 dark:text-gray-200">Klien</span>
                    </button>
                    <button @click="openCreateProductModal()" class="group flex flex-col items-center gap-1.5 rounded-xl p-2 transition hover:bg-gray-50 dark:hover:bg-white/[0.03] active:scale-95">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 shadow-theme-xs dark:bg-amber-500/10 dark:text-amber-400">
                            <x-heroicon-o-archive-box class="h-5 w-5" />
                        </div>
                        <span class="text-[11px] font-medium text-gray-800 dark:text-gray-200">Produk</span>
                    </button>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Aktivitas Invoice Terbaru</h2>
                    <button @click="activeTab = 'invoices'" class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Lihat Semua</button>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($recentInvoices as $invoice)
                        <div class="flex items-center justify-between py-3">
                            <div class="space-y-0.5">
                                <a href="{{ route('mobile.invoices.show', $invoice) }}" class="text-xs font-bold text-brand-600 hover:underline dark:text-brand-400">
                                    {{ $invoice->number }}
                                </a>
                                <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $invoice->client?->name }}</p>
                                <p class="text-[11px] font-semibold text-gray-900 dark:text-white">Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <x-status-badge :status="$invoice->status" />
                                <a href="https://wa.me/?text={{ urlencode('Halo '.$invoice->client?->name.', berikut link invoice Anda: '.route('public.invoices.show', $invoice->public_token)) }}" target="_blank" class="inline-flex items-center gap-1 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    <x-heroicon-o-share class="h-3 w-3" />
                                    WA Link
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center">
                            <img src="{{ asset('images/empty/datatable-empty.png') }}" class="mx-auto h-24 w-auto opacity-90 dark:opacity-75" alt="Belum ada data">
                            <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Belum ada invoice terbaru.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 2: INVOICES & PENAWARAN (Combined Document Tab) -->
        <div x-show="activeTab === 'invoices'" x-data="{ docType: 'invoices' }" class="space-y-3" style="display: none;">
            <div class="flex items-center justify-between">
                <!-- Segment Pill Switcher -->
                <div class="flex rounded-lg border border-gray-200 bg-gray-100 p-1 dark:border-gray-800 dark:bg-gray-900">
                    <button @click="docType = 'invoices'" :class="docType === 'invoices' ? 'bg-white font-semibold text-gray-900 shadow-theme-xs dark:bg-gray-800 dark:text-white' : 'text-gray-500 dark:text-gray-400'" class="rounded-md px-3 py-1 text-xs transition">
                        Invoice
                    </button>
                    <button @click="docType = 'quotations'" :class="docType === 'quotations' ? 'bg-white font-semibold text-gray-900 shadow-theme-xs dark:bg-gray-800 dark:text-white' : 'text-gray-500 dark:text-gray-400'" class="rounded-md px-3 py-1 text-xs transition">
                        Penawaran
                    </button>
                </div>

                <!-- Contextual Add Button -->
                <button x-show="docType === 'invoices'" @click="openCreateInvoiceModal()" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    + Invoice
                </button>
                <button x-show="docType === 'quotations'" @click="openCreateQuotationModal()" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600" style="display: none;">
                    + Penawaran
                </button>
            </div>

            <!-- Sub-tab 1: Invoices List -->
            <div x-show="docType === 'invoices'" class="space-y-2.5">
                @forelse($recentInvoices as $invoice)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-2">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('mobile.invoices.show', $invoice) }}" class="font-semibold text-sm text-brand-600 hover:underline dark:text-brand-400">{{ $invoice->number }}</a>
                            <x-status-badge :status="$invoice->status" />
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->client?->name }} • {{ $invoice->issue_date?->format('d M Y') }}</p>
                        <div class="flex items-center justify-between border-t border-gray-100 pt-2 dark:border-gray-800">
                            <span class="font-semibold text-sm text-gray-900 dark:text-white">Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</span>
                            <div class="flex gap-2 text-xs">
                                <a href="{{ route('mobile.invoices.show', $invoice) }}" class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Detail</a>
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 font-medium text-sky-700 hover:bg-sky-100 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300">PDF</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                        <img src="{{ asset('images/empty/datatable-empty.png') }}" class="mx-auto h-24 w-auto opacity-90 dark:opacity-75" alt="Belum ada data">
                        <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Belum ada invoice dibuat.</p>
                    </div>
                @endforelse
            </div>

            <!-- Sub-tab 2: Quotations List -->
            <div x-show="docType === 'quotations'" class="space-y-2.5" style="display: none;">
                @forelse($recentQuotations as $quotation)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $quotation->number }}</span>
                            <x-status-badge :status="$quotation->status" />
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->client?->name }} • {{ $quotation->issue_date?->format('d M Y') }}</p>
                        <div class="flex items-center justify-between border-t border-gray-100 pt-2 dark:border-gray-800">
                            <span class="font-semibold text-sm text-gray-900 dark:text-white">Rp {{ number_format((float) $quotation->total, 0, ',', '.') }}</span>
                            <a href="{{ route('quotations.show', $quotation) }}" class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Detail</a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                        <img src="{{ asset('images/empty/datatable-empty.png') }}" class="mx-auto h-24 w-auto opacity-90 dark:opacity-75" alt="Belum ada data">
                        <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Belum ada penawaran dibuat.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 3: KLIEN -->
        <div x-show="activeTab === 'clients'" class="space-y-3" style="display: none;">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Daftar Klien</h2>
                <button @click="openCreateClientModal()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    + Tambah Klien
                </button>
            </div>
            <div class="space-y-2.5">
                @forelse($clients as $client)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                        <h3 class="font-semibold text-sm text-gray-900 dark:text-white">{{ $client->name }}</h3>
                        @if($client->company_name)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $client->company_name }}</p>
                        @endif
                        <div class="mt-2 flex gap-3 text-xs text-gray-600 dark:text-gray-400">
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}" class="inline-flex items-center gap-1 text-brand-600 hover:underline">
                                    <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg> {{ $client->phone }}
                                </a>
                            @endif
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="inline-flex items-center gap-1 text-brand-600 hover:underline">
                                    <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> {{ $client->email }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                        <img src="{{ asset('images/empty/datatable-empty.png') }}" class="mx-auto h-24 w-auto opacity-90 dark:opacity-75" alt="Belum ada data">
                        <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Belum ada klien terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 4: PRODUK -->
        <div x-show="activeTab === 'products'" class="space-y-3" style="display: none;">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Katalog Produk</h2>
                <button @click="openCreateProductModal()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    + Tambah Produk
                </button>
            </div>
            <div class="space-y-2.5">
                @forelse($products as $product)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">{{ $product->name }}</h3>
                            <span class="rounded-full border border-gray-200 bg-gray-50 px-2 py-0.5 text-[10px] font-medium text-gray-600 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">per {{ $product->unit }}</span>
                        </div>
                        @if($product->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->description }}</p>
                        @endif
                        <p class="font-semibold text-sm text-brand-600 dark:text-brand-400">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                        <img src="{{ asset('images/empty/datatable-empty.png') }}" class="mx-auto h-24 w-auto opacity-90 dark:opacity-75" alt="Belum ada data">
                        <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Belum ada produk di katalog.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- CREATE INVOICE MODAL FOR MOBILE -->
    <div
        x-show="showInvoiceModal"
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
            x-show="showInvoiceModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            class="flex max-h-[75vh] w-full max-w-lg flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900 sm:rounded-2xl"
        >
            <!-- Fixed Modal Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Buat Invoice Baru</h3>
                <button @click="showInvoiceModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
            </div>

            <!-- Form Wrapper with Scrollable Body and Sticky Action Footer -->
            <form method="POST" action="{{ route('invoices.store') }}" class="flex flex-1 flex-col overflow-hidden" x-data="itemForm({ productData: {{ $productJson }}, existingItems: [], existingTerms: [] })">
                @csrf
                <input type="hidden" name="from_mobile" value="1">
                <!-- Scrollable Form Body -->
                <div class="flex-1 space-y-4 overflow-y-auto p-5 text-xs">
                    <!-- Row 1: Nomor Invoice & Nama Klien -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nomor Invoice</label>
                            <input type="text" name="number" value="INV-{{ now()->format('Ymd-His') }}" required class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nama Klien</label>
                            <select name="client_id" required class="w-full appearance-none rounded-lg border border-gray-200 bg-white py-2.5 pl-3.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.8%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                                <option value="">-- Pilih Klien --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} {{ $c->company_name ? "({$c->company_name})" : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Tanggal Terbit & Jatuh Tempo -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Tanggal Terbit</label>
                            <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Jatuh Tempo</label>
                            <input type="date" name="due_date" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Row 3: Opsional PPN & PPh Toggle -->
                    <div x-data="{ showTax: false }" class="space-y-2 rounded-lg border border-gray-200 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-white/[0.02]">
                        <label class="inline-flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" x-model="showTax" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                            <span>Gunakan Pajak (PPN / PPh)</span>
                        </label>

                        <div x-show="showTax" class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">PPN (%)</label>
                                <input type="number" step="0.01" name="tax_rate" value="0" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">PPh (%)</label>
                                <input type="number" step="0.01" name="pph_rate" value="0" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="down_payment_amount" :value="paymentTerms.length ? paymentTerms[0].amount : 0">

                    <!-- Dynamic Items Table Section -->
                    <div class="space-y-3 pt-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Daftar Item / Produk</h4>
                            <button type="button" x-on:click="addRow()" class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                + Tambah Baris
                            </button>
                        </div>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="space-y-2.5 rounded-lg border border-gray-200 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-white/[0.02]">
                                <input type="hidden" x-bind:name="'items[' + index + '][product_id]'" x-model="item.product_id">
                                <input type="hidden" x-bind:name="'items[' + index + '][description]'" x-model="item.description">
                                <input type="hidden" x-bind:name="'items[' + index + '][quantity]'" x-model="item.quantity">
                                <input type="hidden" x-bind:name="'items[' + index + '][unit_price]'" x-model="item.unit_price">

                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-gray-500" x-text="'Item #' + (index + 1)"></span>
                                    <button type="button" x-on:click="removeRow(index)" x-show="items.length > 1" class="text-rose-500 hover:text-rose-700 p-0.5" title="Hapus Item">
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8H5v12zm5-10h2v8h-2v-8zm4 0h2v8h-2v-8zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                    </button>
                                </div>

                                <select x-model="item.product_id" x-on:change="onSelect(index)" class="w-full appearance-none rounded-md border border-gray-200 bg-white py-2 pl-3.5 pr-10 text-xs text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.8%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                                    <option value="">— Pilih Produk Master —</option>
                                    <template x-for="p in productData" :key="p.id">
                                        <option :value="p.id" x-text="p.name + ' (Rp ' + new Intl.NumberFormat('id-ID').format(p.price) + ')'"></option>
                                    </template>
                                </select>

                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-4">
                                        <label class="mb-0.5 block text-[10px] text-gray-500">Jumlah (Qty)</label>
                                        <input x-on:focus="$el.value = item.quantity; $el.select()" x-on:blur="item.quantity = fixNum($el.value); $el.value = fmt(item.quantity)" x-on:input="item.quantity = fixNum($el.value)" class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-right text-xs text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white" x-bind:value="fmt(item.quantity)">
                                    </div>
                                    <div class="col-span-8">
                                        <label class="mb-0.5 block text-[10px] text-gray-500">Harga Satuan (Rp)</label>
                                        <input x-on:focus="if(!$el.readOnly){ $el.value = item.unit_price; $el.select() }" x-on:blur="if(!$el.readOnly){ item.unit_price = fixNum($el.value); $el.value = fmt(item.unit_price) }" x-bind:readonly="!!item.product_id" x-bind:class="!!item.product_id ? 'bg-gray-100 text-gray-500 dark:bg-gray-800' : 'bg-white dark:bg-gray-900 text-gray-800 dark:text-white'" class="h-10 w-full rounded-md border border-gray-200 px-3 text-right text-xs focus:border-brand-300 focus:outline-none dark:border-gray-800" x-bind:value="fmt(item.unit_price)">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Payment Terms Section (Unified UI with Item List) -->
                    <div class="space-y-3 pt-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Pembayaran Bertahap</h4>
                            <button type="button" x-on:click="addTerm()" class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                + Tambah Termin
                            </button>
                        </div>

                        <template x-for="(term, index) in paymentTerms" :key="index">
                            <div class="space-y-2.5 rounded-lg border border-gray-200 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-white/[0.02]">
                                <input type="hidden" x-bind:name="'payment_terms[' + index + '][label]'" x-model="term.label">
                                <input type="hidden" x-bind:name="'payment_terms[' + index + '][amount]'" x-model="term.amount">
                                <input type="hidden" x-bind:name="'payment_terms[' + index + '][due_date]'" x-model="term.due_date">

                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-gray-500" x-text="'Termin #' + (index + 1)"></span>
                                    <button type="button" x-on:click="removeTerm(index)" class="text-rose-500 hover:text-rose-700 p-0.5" title="Hapus termin">
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8H5v12zm5-10h2v8h-2v-8zm4 0h2v8h-2v-8zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                    </button>
                                </div>

                                <div>
                                    <input x-model="term.label" class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-xs text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white" placeholder="Nama termin (Contoh: DP 50%)">
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="mb-0.5 block text-[10px] text-gray-500">Nominal (Rp)</label>
                                        <input x-on:focus="$el.value = term.amount; $el.select()" x-on:blur="clampTermAmount(index, $el); $el.value = fmt(term.amount)" x-on:input="$el.value = moneyDigits($el.value, 12); clampTermAmount(index, $el)" x-bind:value="fmt(term.amount)" inputmode="numeric" class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-right text-xs text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="mb-0.5 block text-[10px] text-gray-500">Jatuh Tempo</label>
                                        <input type="date" x-model="term.due_date" class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-xs text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Notes / Footer Section -->
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Catatan / Rekening Transfer</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-200 bg-transparent p-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white" placeholder="Catatan footer invoice...">{{ $defaultNotes }}</textarea>
                    </div>
                </div>

                <!-- Sticky Action Footer (Always Visible at Bottom of Sheet) -->
                <div class="shrink-0 border-t border-gray-100 bg-white/95 p-4 backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                        Simpan & Terbitkan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CREATE QUOTATION MODAL FOR MOBILE -->
    <div
        x-show="showQuotationModal"
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
            x-show="showQuotationModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            class="flex max-h-[75vh] w-full max-w-lg flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900 sm:rounded-2xl"
        >
            <!-- Fixed Modal Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Buat Penawaran Baru</h3>
                <button @click="showQuotationModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
            </div>

            <!-- Form Wrapper with Scrollable Body and Sticky Action Footer -->
            <form method="POST" action="{{ route('quotations.store') }}" class="flex flex-1 flex-col overflow-hidden" x-data="itemForm({ productData: {{ $productJson }}, existingItems: [], existingTerms: [] })">
                @csrf
                <input type="hidden" name="from_mobile" value="1">
                <!-- Scrollable Form Body -->
                <div class="flex-1 space-y-4 overflow-y-auto p-5 text-xs">
                    <!-- Row 1: Nomor Penawaran & Nama Klien -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nomor Penawaran</label>
                            <input type="text" name="number" value="QUO-{{ now()->format('Ymd-His') }}" required class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nama Klien</label>
                            <select name="client_id" required class="w-full appearance-none rounded-lg border border-gray-200 bg-white py-2.5 pl-3.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.8%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                                <option value="">-- Pilih Klien --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} {{ $c->company_name ? "({$c->company_name})" : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Tanggal Terbit & Masa Berlaku -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Tanggal Terbit</label>
                            <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Masa Berlaku (S.d.)</label>
                            <input type="date" name="valid_until" value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Row 3: Opsional PPN & PPh Toggle -->
                    <div x-data="{ showTax: false }" class="space-y-2 rounded-lg border border-gray-200 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-white/[0.02]">
                        <label class="inline-flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" x-model="showTax" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                            <span>Gunakan Pajak (PPN / PPh)</span>
                        </label>

                        <div x-show="showTax" class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">PPN (%)</label>
                                <input type="number" step="0.01" name="tax_rate" value="0" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">PPh (%)</label>
                                <input type="number" step="0.01" name="pph_rate" value="0" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Items Table Section -->
                    <div class="space-y-3 pt-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Daftar Item / Produk</h4>
                            <button type="button" x-on:click="addRow()" class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                + Tambah Baris
                            </button>
                        </div>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="space-y-2.5 rounded-lg border border-gray-200 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-white/[0.02]">
                                <input type="hidden" x-bind:name="'items[' + index + '][product_id]'" x-model="item.product_id">
                                <input type="hidden" x-bind:name="'items[' + index + '][description]'" x-model="item.description">
                                <input type="hidden" x-bind:name="'items[' + index + '][quantity]'" x-model="item.quantity">
                                <input type="hidden" x-bind:name="'items[' + index + '][unit_price]'" x-model="item.unit_price">

                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-gray-500" x-text="'Item #' + (index + 1)"></span>
                                    <button type="button" x-on:click="removeRow(index)" x-show="items.length > 1" class="text-rose-500 hover:text-rose-700 p-0.5" title="Hapus Item">
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8H5v12zm5-10h2v8h-2v-8zm4 0h2v8h-2v-8zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                    </button>
                                </div>

                                <select x-model="item.product_id" x-on:change="onSelect(index)" class="w-full appearance-none rounded-md border border-gray-200 bg-white py-2 pl-3.5 pr-10 text-xs text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.8%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                                    <option value="">— Pilih Produk Master —</option>
                                    <template x-for="p in productData" :key="p.id">
                                        <option :value="p.id" x-text="p.name + ' (Rp ' + new Intl.NumberFormat('id-ID').format(p.price) + ')'"></option>
                                    </template>
                                </select>

                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-4">
                                        <label class="mb-0.5 block text-[10px] text-gray-500">Jumlah (Qty)</label>
                                        <input x-on:focus="$el.value = item.quantity; $el.select()" x-on:blur="item.quantity = fixNum($el.value); $el.value = fmt(item.quantity)" x-on:input="item.quantity = fixNum($el.value)" class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-right text-xs text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white" x-bind:value="fmt(item.quantity)">
                                    </div>
                                    <div class="col-span-8">
                                        <label class="mb-0.5 block text-[10px] text-gray-500">Harga Satuan (Rp)</label>
                                        <input x-on:focus="if(!$el.readOnly){ $el.value = item.unit_price; $el.select() }" x-on:blur="if(!$el.readOnly){ item.unit_price = fixNum($el.value); $el.value = fmt(item.unit_price) }" x-bind:readonly="!!item.product_id" x-bind:class="!!item.product_id ? 'bg-gray-100 text-gray-500 dark:bg-gray-800' : 'bg-white dark:bg-gray-900 text-gray-800 dark:text-white'" class="h-10 w-full rounded-md border border-gray-200 px-3 text-right text-xs focus:border-brand-300 focus:outline-none dark:border-gray-800" x-bind:value="fmt(item.unit_price)">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Notes / Footer Section -->
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Catatan / Syarat Ketentuan</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-200 bg-transparent p-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white" placeholder="Catatan footer penawaran...">Penawaran ini berlaku hingga tanggal yang tertera di atas.</textarea>
                    </div>
                </div>

                <!-- Sticky Action Footer -->
                <div class="shrink-0 border-t border-gray-100 bg-white/95 p-4 backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                        Simpan & Terbitkan Penawaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CREATE CLIENT MODAL FOR MOBILE -->
    <div
        x-show="showClientModal"
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
            x-show="showClientModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            class="flex max-h-[75vh] w-full max-w-md flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900 sm:rounded-2xl"
        >
            <!-- Fixed Modal Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tambah Klien Baru</h3>
                <button @click="showClientModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
            </div>

            <!-- Form Wrapper with Scrollable Body and Sticky Action Footer -->
            <form method="POST" action="{{ route('clients.store') }}" class="flex flex-1 flex-col overflow-hidden">
                @csrf
                <input type="hidden" name="from_mobile" value="1">
                <!-- Scrollable Form Body -->
                <div class="flex-1 space-y-3 overflow-y-auto p-5 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nama Klien / Kontak</label>
                            <input type="text" name="name" required placeholder="Ahmad Susanto" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nama Perusahaan (Opsional)</label>
                            <input type="text" name="company_name" placeholder="PT Nusa Indah" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" placeholder="klien@perusahaan.com" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Telepon / WA</label>
                            <input type="text" name="phone" placeholder="08123456789" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                        <textarea name="address" rows="2" placeholder="Jl. Merdeka No. 123..." class="w-full rounded-lg border border-gray-200 bg-transparent p-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-white"></textarea>
                    </div>
                </div>

                <!-- Sticky Action Footer -->
                <div class="shrink-0 border-t border-gray-100 bg-white/95 p-4 backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                        Simpan Klien
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CREATE PRODUCT MODAL FOR MOBILE -->
    <div
        x-show="showProductModal"
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
            x-show="showProductModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            class="flex max-h-[75vh] w-full max-w-md flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900 sm:rounded-2xl"
        >
            <!-- Fixed Modal Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tambah Produk Baru</h3>
                <button @click="showProductModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
            </div>

            <!-- Form Wrapper with Scrollable Body and Sticky Action Footer -->
            <form method="POST" action="{{ route('products.store') }}" class="flex flex-1 flex-col overflow-hidden">
                @csrf
                <input type="hidden" name="from_mobile" value="1">
                <!-- Scrollable Form Body -->
                <div class="flex-1 space-y-3 overflow-y-auto p-5 text-xs">
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nama Produk / Jasa</label>
                        <input type="text" name="name" required placeholder="Contoh: Desain Landing Page" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Harga Satuan (Rp)</label>
                            <input type="number" step="0.01" name="price" required placeholder="1500000" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                            <input type="text" name="unit" value="pcs" required placeholder="pcs, jam, paket" class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Deskripsi Ringkas (Opsional)</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-200 bg-transparent p-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white" placeholder="Deskripsi spesifikasi produk..."></textarea>
                    </div>
                </div>

                <!-- Sticky Action Footer -->
                <div class="shrink-0 border-t border-gray-100 bg-white/95 p-4 backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MOBILE SETTINGS MODAL -->
    <div
        x-show="showSettingsModal"
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
            x-show="showSettingsModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
            class="flex max-h-[85vh] w-full max-w-md flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900 sm:rounded-2xl"
        >
            <!-- Fixed Modal Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 fill-brand-600 dark:fill-brand-400" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Pengaturan Aplikasi & Akun</h3>
                </div>
                <button @click="showSettingsModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
            </div>

            <!-- Scrollable Settings Content -->
            <div class="flex-1 space-y-4 overflow-y-auto p-5 text-xs">
                <!-- SECTION 1: PROFIL PERUSAHAAN -->
                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-800/40">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Profil Perusahaan</span>
                            <h4 class="mt-0.5 text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->company?->name ?? 'Paperwork' }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
                        </div>
                        <a href="{{ route('mobile.profile') }}" class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            Edit Profil
                        </a>
                    </div>
                </div>

                <!-- SECTION 2: INFORMASI BILLING PLAN -->
                <div class="rounded-xl border border-brand-100 bg-brand-50/60 p-4 dark:border-brand-500/20 dark:bg-brand-500/10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 fill-brand-600 dark:fill-brand-400" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white">Informasi Paket Langganan</h4>
                                <span class="text-[11px] font-medium text-brand-700 dark:text-brand-300">
                                    Status: {{ ucfirst(auth()->user()->company?->subscription_status ?? 'Free Trial 30 Hari') }}
                                </span>
                            </div>
                        </div>
                        <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-[10px] font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">
                            PRO
                        </span>
                    </div>

                    @php
                        $trialEnds = auth()->user()->company?->trial_ends_at;
                        $daysLeft = $trialEnds ? max(0, (int) now()->diffInDays($trialEnds, false)) : 30;
                    @endphp
                    <div class="mt-3 flex items-center justify-between border-t border-brand-200/60 pt-3 text-xs dark:border-brand-500/20">
                        <span class="text-gray-600 dark:text-gray-400">Masa berlaku langganan:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $daysLeft }} Hari Tersisa</span>
                    </div>

                    <a href="{{ route('mobile.billing') }}" class="mt-3 inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-3 text-xs font-semibold text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                        Upgrade / Kelola Langganan (Pakasir)
                    </a>
                </div>

                <!-- SECTION 3: KELOLA AKUN REKENING BANK -->
                <div x-data="{ showAddBank: false }" class="rounded-xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white">Rekening Bank Perusahaan</h4>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Digunakan untuk instruksi pembayaran invoice</p>
                        </div>
                        <button @click="showAddBank = !showAddBank" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-brand-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-brand-400">
                            <span x-text="showAddBank ? 'Batal' : '+ Rekening'"></span>
                        </button>
                    </div>

                    <!-- List of Registered Bank Accounts -->
                    <div class="space-y-2">
                        @forelse($bankAccounts as $acc)
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50/70 p-2.5 dark:border-gray-800 dark:bg-gray-800/40">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $acc->bank_name }}</span>
                                        @if($acc->is_primary)
                                            <span class="rounded-full bg-emerald-100 px-1.5 py-0.2 text-[9px] font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Utama</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] font-mono text-gray-600 dark:text-gray-300">{{ $acc->account_number }}</p>
                                    <p class="text-[10px] text-gray-400">a/n {{ $acc->account_name }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="py-2 text-center text-xs text-gray-400">Belum ada rekening bank ditambahkan.</p>
                        @endforelse
                    </div>

                    <!-- Form Tambah Rekening Inline -->
                    <form x-show="showAddBank" method="POST" action="{{ route('settings.bank-accounts.store') }}" class="mt-3 space-y-2.5 border-t border-gray-100 pt-3 dark:border-gray-800" style="display: none;">
                        @csrf
                        <input type="hidden" name="from_mobile" value="1">
                        <div>
                            <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nama Bank</label>
                            <input type="text" name="bank_name" required placeholder="BCA / Mandiri / BRI" class="h-9 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:text-white">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Nomor Rekening</label>
                                <input type="text" name="account_number" required placeholder="1234567890" class="h-9 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Atas Nama</label>
                                <input type="text" name="account_name" required placeholder="PT Perusahaan" class="h-9 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none dark:border-gray-800 dark:text-white">
                            </div>
                        </div>
                        <input type="hidden" name="currency" value="IDR">
                        <button type="submit" class="inline-flex h-9 w-full items-center justify-center gap-1 rounded-lg bg-brand-500 px-3 text-xs font-semibold text-white shadow-theme-xs hover:bg-brand-600 active:scale-[0.98]">
                            + Simpan Rekening Baru
                        </button>
                    </form>
                </div>

                <!-- SECTION 4: MODAL UTILITY ACTIONS -->
                <div class="space-y-2 pt-2">
                    <button @click="$store.theme.toggle()" class="flex h-10 w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                        <span>Mode Gelap / Terang</span>
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.3 2a10 10 0 0 0 9.7 11.5 10 10 0 1 1-11.5-9.7 7.5 7.5 0 0 0 1.8-1.8z"/></svg>
                    </button>

                    <a href="{{ route('dashboard', ['desktop' => 1]) }}" class="flex h-10 w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                        <span>Beralih ke Tampilan Desktop</span>
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 3H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h7v2H8v2h8v-2h-3v-2h7c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 12H4V5h16v10z"/></svg>
                    </a>

                    <a href="{{ route('mobile.profile') }}" class="flex h-10 w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                        <span>Edit Profil Perusahaan</span>
                        <x-heroicon-o-user-circle class="h-4 w-4" />
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex h-10 w-full items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 text-xs font-semibold text-rose-700 shadow-theme-xs hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-400">
                            <span>Keluar dari Akun (Logout)</span>
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bottom App Navigation Bar (5 Main Items) -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-200 bg-white/95 px-3 py-2 backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
        <div class="mx-auto flex max-w-md items-center justify-around">
            <button @click="activeTab = 'home'" :class="{'text-brand-600 dark:text-brand-400 font-bold': activeTab === 'home', 'text-gray-400 dark:text-gray-500': activeTab !== 'home'}" class="flex flex-col items-center gap-1 text-[11px]">
                <x-heroicon-s-home x-show="activeTab === 'home'" class="h-5 w-5" />
                <x-heroicon-o-home x-show="activeTab !== 'home'" class="h-5 w-5" />
                <span>Home</span>
            </button>
            <button @click="activeTab = 'invoices'" :class="{'text-brand-600 dark:text-brand-400 font-bold': activeTab === 'invoices', 'text-gray-400 dark:text-gray-500': activeTab !== 'invoices'}" class="flex flex-col items-center gap-1 text-[11px]">
                <x-heroicon-s-document-text x-show="activeTab === 'invoices'" class="h-5 w-5" />
                <x-heroicon-o-document-text x-show="activeTab !== 'invoices'" class="h-5 w-5" />
                <span>Invoice</span>
            </button>

            <!-- Floating Action Button -->
            <button @click="openCreateInvoiceModal()" class="-mt-6 flex h-12 w-12 items-center justify-center rounded-full bg-brand-500 text-white shadow-theme-md transition hover:bg-brand-600 active:scale-95">
                <x-heroicon-o-plus class="h-6 w-6" />
            </button>

            <button @click="activeTab = 'clients'" :class="{'text-brand-600 dark:text-brand-400 font-bold': activeTab === 'clients', 'text-gray-400 dark:text-gray-500': activeTab !== 'clients'}" class="flex flex-col items-center gap-1 text-[11px]">
                <x-heroicon-s-user-group x-show="activeTab === 'clients'" class="h-5 w-5" />
                <x-heroicon-o-user-group x-show="activeTab !== 'clients'" class="h-5 w-5" />
                <span>Klien</span>
            </button>
            <button @click="activeTab = 'products'" :class="{'text-brand-600 dark:text-brand-400 font-bold': activeTab === 'products', 'text-gray-400 dark:text-gray-500': activeTab !== 'products'}" class="flex flex-col items-center gap-1 text-[11px]">
                <x-heroicon-s-archive-box x-show="activeTab === 'products'" class="h-5 w-5" />
                <x-heroicon-o-archive-box x-show="activeTab !== 'products'" class="h-5 w-5" />
                <span>Produk</span>
            </button>
        </div>
    </nav>
</div>

<script>
document.addEventListener('alpine:init', () => {
    const fmt = (n) => new Intl.NumberFormat('id-ID').format(n || 0);

    Alpine.data('itemForm', ({ productData, existingItems, existingTerms }) => ({
        productData,
        items: (existingItems && existingItems.length > 0)
            ? existingItems
            : [{ product_id: '', description: '', quantity: 1, unit_price: 0 }],
        paymentTerms: (existingTerms && existingTerms.length > 0)
            ? existingTerms
            : [],

        onSelect(index) {
            const pid = this.items[index].product_id;
            const p = this.productData.find(p => String(p.id) === String(pid));
            if (p) {
                this.items[index].description = p.description;
                this.items[index].unit_price = p.price;
            } else {
                this.items[index].description = '';
                this.items[index].unit_price = 0;
            }
        },

        addRow() {
            this.items.push({ product_id: '', description: '', quantity: 1, unit_price: 0 });
        },

        removeRow(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        addTerm() {
            this.paymentTerms.push({
                label: 'Termin ' + (this.paymentTerms.length + 1),
                amount: 0,
                due_date: '',
            });
        },

        removeTerm(index) {
            this.paymentTerms.splice(index, 1);
        },

        get subtotal() {
            return this.items.reduce((total, item) => total + (Number(item.quantity) * Number(item.unit_price)), 0);
        },

        get taxRate() {
            return this.fixNum(document.querySelector('[name="tax_rate"]')?.value ?? 0);
        },

        get pphRate() {
            return this.fixNum(document.querySelector('[name="pph_rate"]')?.value ?? 0);
        },

        get invoiceTotal() {
            const subtotal = this.subtotal;
            const taxTotal = subtotal * (this.taxRate / 100);
            const pphTotal = subtotal * (this.pphRate / 100);

            return Math.max(subtotal + taxTotal - pphTotal, 0);
        },

        termsTotalExcept(index) {
            return this.paymentTerms.reduce((total, term, termIndex) => {
                return termIndex === index ? total : total + Number(term.amount || 0);
            }, 0);
        },

        maxTermAmount(index) {
            return Math.max(this.invoiceTotal - this.termsTotalExcept(index), 0);
        },

        clampTermAmount(index, input = null) {
            const enteredAmount = this.fixMoney(input?.value ?? this.paymentTerms[index]?.amount ?? 0, 12);
            const maxAmount = this.maxTermAmount(index);
            const nextAmount = Math.min(enteredAmount, maxAmount);

            this.paymentTerms[index].amount = nextAmount;

            if (input && enteredAmount > maxAmount) {
                input.value = String(Math.trunc(nextAmount));
            }
        },

        fixNum(raw) {
            const cleaned = String(raw).replace(/[^\d,.-]/g, '').replace('.', '').replace(',', '.');
            const n = parseFloat(cleaned);
            return isNaN(n) || n < 0 ? 0 : n;
        },

        moneyDigits(raw, maxDigits = 12) {
            return String(raw).replace(/\D/g, '').slice(0, maxDigits);
        },

        fixMoney(raw, maxDigits = 12) {
            const digits = this.moneyDigits(raw, maxDigits);
            const n = Number(digits);

            return Number.isFinite(n) && n > 0 ? n : 0;
        },

        fmt,
    }));
});

function mobileAppWorkspace() {
    return {
        activeTab: 'home',
        showInvoiceModal: false,
        showQuotationModal: false,
        showClientModal: false,
        showProductModal: false,
        showSettingsModal: false,

        // Period filter state
        filterMonth: String(new Date().getMonth() + 1),
        filterYear: String(new Date().getFullYear()),
        statsLoading: false,
        statsData: {
            revenue_formatted: 'Rp {{ number_format($stats["revenue_this_month"], 0, ",", ".") }}',
            unpaid_balance_formatted: 'Rp {{ number_format($stats["unpaid_balance"], 0, ",", ".") }}',
            overdue_count: {{ $stats['overdue_count'] }},
        },

        async fetchStats() {
            this.statsLoading = true;
            try {
                const url = `{{ route('mobile.stats') }}?month=${this.filterMonth}&year=${this.filterYear}`;
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    this.statsData = await res.json();
                }
            } catch (e) {
                console.error('Failed to fetch stats:', e);
            } finally {
                this.statsLoading = false;
            }
        },

        openCreateInvoiceModal() {
            this.showInvoiceModal = true;
        },
        openCreateQuotationModal() {
            this.showQuotationModal = true;
        },
        openCreateClientModal() {
            this.showClientModal = true;
        },
        openCreateProductModal() {
            this.showProductModal = true;
        },
        openSettingsModal() {
            this.showSettingsModal = true;
        }
    }
}
</script>
@endsection

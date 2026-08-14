@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white/90">Invoice</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola tagihan invoice pelanggan dan pelacakan pembayaran.</p>
        </div>
        <button type="button" @click="$dispatch('open-modal', 'create-invoice')" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
            Buat Invoice
        </button>
    </div>

    <!-- Stats Widgets (4 columns) -->
    @if (!empty($invoiceStats))
        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @foreach ($invoiceStats as $stat)
                @php
                    $statTheme = [
                        'border-sky-100 bg-sky-50/80 text-sky-700 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-300',
                        'border-emerald-100 bg-emerald-50/80 text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                        'border-amber-100 bg-amber-50/80 text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300',
                        'border-violet-100 bg-violet-50/80 text-violet-700 dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-300',
                    ][$loop->index % 4];
                @endphp
                <div class="rounded-lg border p-[0.85rem] shadow-theme-xs {{ $statTheme }}">
                    <div class="flex items-start justify-between gap-3">
                        <p class="truncate text-sm font-medium text-current/75">{{ $stat['label'] }}</p>
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/70 text-current shadow-theme-xs dark:bg-white/10">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-xl font-semibold text-gray-950 dark:text-white/90">{{ $stat['value'] }}</p>
                    <p class="mt-1 text-xs text-current/65">{{ $stat['meta'] }}</p>
                </div>
            @endforeach
        </section>
    @endif

    <!-- DataTable Section -->
    <section class="rounded-lg border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action="{{ route('invoices.bulk-delete') }}" x-data="{ hasSelection: false }" @change="hasSelection = document.querySelectorAll('.dt-checkbox:checked').length > 0">
            @csrf
            <div class="border-b border-gray-100 p-5 dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <template x-if="hasSelection">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus invoice yang dipilih?')" class="inline-flex items-center justify-center rounded-lg bg-error-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-error-600">
                                Hapus Terpilih
                            </button>
                        </template>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative w-full sm:w-56">
                            <select
                                id="invoices-payment-status-filter"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            >
                                <option value="">Semua Status Pembayaran</option>
                                <option value="unpaid">Belum Dibayar</option>
                                <option value="partial">Dibayar Sebagian</option>
                                <option value="paid">Lunas</option>
                                <option value="draft">Draft</option>
                                <option value="void">Batal</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 dark:text-gray-500">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <div class="relative w-full sm:w-80">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.5 17.5L13.875 13.875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <input
                                id="invoices-table-search"
                                type="search"
                                placeholder="Cari nomor invoice, klien..."
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table
                    class="min-w-full text-left text-sm"
                    data-ajax-datatable
                    data-ajax-url="{{ route('invoices.index', ['datatable' => 1]) }}"
                    data-columns='[
                        {"data":"checkbox","orderable":false,"searchable":false,"className":"w-10"},
                        {"data":"number"},
                        {"data":"client"},
                        {"data":"total"},
                        {"data":"status"},
                        {"data":"date"},
                        {"data":"action","className":"dt-action-cell"}
                    ]'
                    data-page-length="10"
                    data-search-target="#invoices-table-search"
                    data-filter-target="#invoices-payment-status-filter"
                    data-filter-param="payment_status">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <input type="checkbox" class="size-4 cursor-pointer rounded border-gray-300 bg-gray-50 text-brand-600 shadow-theme-xs transition-colors focus:ring-2 focus:ring-brand-500/20 focus:ring-offset-0 dark:border-gray-700 dark:bg-gray-900" @change="document.querySelectorAll('.dt-checkbox').forEach(c => c.checked = $event.target.checked); hasSelection = document.querySelectorAll('.dt-checkbox:checked').length > 0">
                            </th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nomor</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Klien</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-transparent"></tbody>
                </table>
            </div>
        </form>
    </section>

    <!-- Edit Modal (Outside the Datatable, triggered conditionally via redirect) -->
    @if ($editInvoice)
        <x-ui.modal name="edit-invoice-{{ $editInvoice->id }}" :is-open="true" class="max-w-5xl p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Edit Invoice</h2>
            @include('invoices.form', ['invoice' => $editInvoice, 'action' => route('invoices.update', $editInvoice), 'method' => 'PUT'])
        </x-ui.modal>
    @endif

    <!-- Create Modal -->
    <x-ui.modal name="create-invoice" :is-open="request('modal') === 'create'" class="max-w-5xl p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Buat Invoice</h2>
        @include('invoices.form', ['invoice' => null, 'action' => route('invoices.store'), 'method' => 'POST'])
    </x-ui.modal>
</div>
@endsection

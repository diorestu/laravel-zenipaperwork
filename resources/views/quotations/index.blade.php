@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white/90">Penawaran</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola penawaran harga barang/jasa kepada pelanggan.</p>
        </div>
        <button type="button" @click="$dispatch('open-modal', 'create-quotation')" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
            Buat Penawaran
        </button>
    </div>

    <!-- DataTable Section -->
    <section class="rounded-lg border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-100 p-5 dark:border-gray-800">
            <div class="flex justify-end">
                <div class="relative w-full sm:w-80">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17.5 17.5L13.875 13.875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input
                        id="quotations-table-search"
                        type="search"
                        placeholder="Cari nomor penawaran, klien..."
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table
                class="min-w-full text-left text-sm"
                data-ajax-datatable
                data-ajax-url="{{ route('quotations.index', ['datatable' => 1]) }}"
                data-columns='[
                    {"data":"number"},
                    {"data":"client"},
                    {"data":"total"},
                    {"data":"status"},
                    {"data":"date"},
                    {"data":"action","className":"dt-action-cell"}
                ]'
                data-page-length="10"
                data-search-target="#quotations-table-search">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
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
    </section>

    <!-- Edit Modal (Outside the Datatable, triggered conditionally via redirect) -->
    @if ($editQuotation)
        <x-ui.modal name="edit-quotation-{{ $editQuotation->id }}" :is-open="true" class="max-w-5xl p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Edit Penawaran</h2>
            @include('quotations.form', ['quotation' => $editQuotation, 'action' => route('quotations.update', $editQuotation), 'method' => 'PUT'])
        </x-ui.modal>
    @endif

    <!-- Create Modal -->
    <x-ui.modal name="create-quotation" :is-open="request('modal') === 'create'" class="max-w-5xl p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Buat Penawaran</h2>
        @include('quotations.form', ['quotation' => null, 'action' => route('quotations.store'), 'method' => 'POST'])
    </x-ui.modal>
</div>
@endsection

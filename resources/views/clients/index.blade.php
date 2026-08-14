@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white/90">Klien</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola data klien, kontak, dan aktivitas dokumen.</p>
        </div>
        <button type="button" @click="$dispatch('open-modal', 'create-client')" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
            Tambah Klien
        </button>
    </div>

    @if (($clientStats[0]['value'] ?? 0) > 0)
        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @foreach ($clientStats as $stat)
                <div class="rounded-lg border border-gray-200 bg-white p-[0.85rem] shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between gap-3">
                        <p class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 11C17.6569 11 19 9.65685 19 8C19 6.34315 17.6569 5 16 5C14.3431 5 13 6.34315 13 8C13 9.65685 14.3431 11 16 11Z" stroke="currentColor" stroke-width="1.7" />
                            <path d="M8 12C9.65685 12 11 10.6569 11 9C11 7.34315 9.65685 6 8 6C6.34315 6 5 7.34315 5 9C5 10.6569 6.34315 12 8 12Z" stroke="currentColor" stroke-width="1.7" />
                            <path d="M3 19C3.61448 16.6667 5.33333 15.5 8.15655 15.5C10.9798 15.5 12.5942 16.6667 13 19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            <path d="M13.5 15C14.25 14.6667 15.0833 14.5 16 14.5C18.3333 14.5 19.6667 15.5 20 17.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-xl font-semibold text-gray-950 dark:text-white/90">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </section>
    @endif

    <section class="rounded-lg border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action="{{ route('clients.bulk-delete') }}" x-data="{ hasSelection: false }" @change="hasSelection = document.querySelectorAll('.dt-checkbox:checked').length > 0">
            @csrf
            <div class="border-b border-gray-100 p-5 dark:border-gray-800">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">Data Klien</h2>
                        <template x-if="hasSelection">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus klien yang dipilih?')" class="inline-flex items-center justify-center rounded-lg bg-error-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-error-600">
                                Hapus Terpilih
                            </button>
                        </template>
                    </div>
                    <div class="relative w-full sm:w-80">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M17.5 17.5L13.875 13.875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <input
                            id="clients-table-search"
                            type="search"
                            placeholder="Cari nama, perusahaan, email, telepon"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-white pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        >
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table
                    class="min-w-full text-left text-sm"
                    data-ajax-datatable
                    data-ajax-url="{{ route('clients.index', ['datatable' => 1]) }}"
                    data-columns='[
                        {"data":"checkbox","orderable":false,"searchable":false,"className":"w-10"},
                        {"data":"client"},
                        {"data":"contact"},
                        {"data":"documents"},
                        {"data":"invoice_value"},
                        {"data":"unpaid_value"},
                        {"data":"action","className":"dt-action-cell"}
                    ]'
                    data-page-length="10"
                    data-search-target="#clients-table-search">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <input type="checkbox" class="size-4 cursor-pointer rounded border-gray-300 bg-gray-50 text-brand-600 shadow-theme-xs transition-colors focus:ring-2 focus:ring-brand-500/20 focus:ring-offset-0 dark:border-gray-700 dark:bg-gray-900" @change="document.querySelectorAll('.dt-checkbox').forEach(c => c.checked = $event.target.checked); hasSelection = document.querySelectorAll('.dt-checkbox:checked').length > 0">
                            </th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Klien</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Kontak</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Dokumen</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nilai Invoice</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Belum Terbayar</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-transparent"></tbody>
                </table>
            </div>
        </form>
    </section>

    <x-ui.modal name="edit-client" class="max-w-2xl p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Edit Klien</h2>
        <form method="POST" data-edit-form="edit-client" data-update-url="{{ route('clients.update', '__ID__') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id">
            <x-form.input name="name" label="Nama" />
            <x-form.input name="company_name" label="Nama Perusahaan" />
            <x-form.input name="email" label="Email" type="email" />
            <x-form.input name="phone" label="Telepon" />
            <x-form.input name="tax_number" label="Nomor Pajak" />
            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</span>
                <span class="block">
                    <textarea name="address" rows="3" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500"></textarea>
                </span>
            </label>
            <div class="flex justify-end">
                <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Simpan</button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="create-client" :is-open="request('modal') === 'create'" class="max-w-2xl p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Tambah Klien</h2>
        <form method="POST" action="{{ route('clients.store') }}" class="mt-4 space-y-4">
            @csrf
            <x-form.input name="name" label="Nama" />
            <x-form.input name="company_name" label="Nama Perusahaan" />
            <x-form.input name="email" label="Email" type="email" />
            <x-form.input name="phone" label="Telepon" />
            <x-form.input name="tax_number" label="Nomor Pajak" />
            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</span>
                <span class="block">
                    <textarea name="address" rows="3" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500">{{ old('address') }}</textarea>
                    @error('address')<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
                </span>
            </label>
            <div class="flex justify-end">
                <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Simpan</button>
            </div>
        </form>
    </x-ui.modal>
</div>
@endsection

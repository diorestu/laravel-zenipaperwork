@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white/90">Products</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola katalog product dan service untuk invoice serta quotation.</p>
        </div>
        <button type="button" @click="$dispatch('open-modal', 'create-product')" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
            Create Product
        </button>
    </div>

    @if (($productStats[0]['value'] ?? 0) > 0)
        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @foreach ($productStats as $stat)
                <div class="rounded-lg border border-gray-200 bg-white p-[0.85rem] shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between gap-3">
                        <p class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7.5L12 3L20 7.5V16.5L12 21L4 16.5V7.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                            <path d="M4.5 8L12 12.25L19.5 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 12.25V20.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-xl font-semibold text-gray-950 dark:text-white/90">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </section>
    @endif

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
                        id="products-table-search"
                        type="search"
                        placeholder="Cari nama, deskripsi, unit"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table
                class="min-w-full text-left text-sm"
                data-ajax-datatable
                data-ajax-url="{{ route('products.index', ['datatable' => 1]) }}"
                data-columns='[
                    {"data":"product"},
                    {"data":"price"},
                    {"data":"usage"},
                    {"data":"status"},
                    {"data":"updated"},
                    {"data":"action","className":"dt-action-cell"}
                ]'
                data-page-length="10"
                data-search-target="#products-table-search">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Product</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Price</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Usage</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Updated</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-transparent"></tbody>
            </table>
        </div>
    </section>

    <x-ui.modal name="edit-product" class="max-w-2xl p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Edit Product</h2>
        <form method="POST" data-edit-form="edit-product" data-update-url="{{ route('products.update', '__ID__') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id">
            <x-form.input name="name" label="Name" />
            <x-form.input name="price" label="Price" type="number" />
            <x-form.input name="unit" label="Unit" />
            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Description</span>
                <span class="block">
                    <textarea name="description" rows="3" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500"></textarea>
                </span>
            </label>
            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300">
                    Active product
                </span>
            </label>
            <div class="flex justify-end">
                <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Save</button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="create-product" :is-open="request('modal') === 'create'" class="max-w-2xl p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Tambah Product</h2>
        <form method="POST" action="{{ route('products.store') }}" class="mt-4 space-y-4">
            @csrf
            <x-form.input name="name" label="Name" />
            <x-form.input name="price" label="Price" type="number" />
            <x-form.input name="unit" label="Unit" value="service" />
            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Description</span>
                <span class="block">
                    <textarea name="description" rows="3" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500">{{ old('description') }}</textarea>
                    @error('description')<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
                </span>
            </label>
            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                    Active product
                </span>
            </label>
            <div class="flex justify-end">
                <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Save</button>
            </div>
        </form>
    </x-ui.modal>
</div>
@endsection

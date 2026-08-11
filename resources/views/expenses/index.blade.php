@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pencatatan Pengeluaran</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola dan catat semua beban operasional serta pengeluaran proyek perusahaan Anda.</p>
        </div>
        <button type="button" @click="$dispatch('open-modal', 'create-expense')" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengeluaran
        </button>
    </div>

    <!-- Stat Cards -->
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Pengeluaran Bulan Ini</p>
            <h3 class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format((float) $totalExpensesThisMonth, 0, ',', '.') }}</h3>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Pengeluaran Tahun Ini</p>
            <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format((float) $totalExpensesThisYear, 0, ',', '.') }}</h3>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Kategori Pengeluaran Terbesar</p>
            <h3 class="mt-2 text-lg font-bold text-brand-600 dark:text-brand-400">
                {{ $topCategory ? $topCategory->category.' (Rp '.number_format((float) $topCategory->total_amount, 0, ',', '.').')' : '-' }}
            </h3>
        </div>
    </div>

    <!-- Expense Datatable Table -->
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Daftar Pengeluaran</h2>
            <div class="flex items-center gap-2">
                <select id="filter-category" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <option value="">— Semua Kategori —</option>
                    @foreach (['Operasional', 'Gaji & Honor', 'Sewa & Utilitas', 'Peralatan & Perlengkapan', 'Marketing & Iklan', 'Modal Proyek', 'Lain-lain'] as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="expenses-table" 
                   class="w-full text-left text-sm text-gray-500 dark:text-gray-400"
                   data-ajax-url="{{ route('expenses.index', ['datatable' => 1]) }}">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 dark:bg-white/5 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Keterangan / Proyek</th>
                        <th class="px-4 py-3 text-right">Nominal</th>
                        <th class="px-4 py-3 text-center">Bukti Nota</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <!-- Loaded via Datatable JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create Expense -->
    <x-ui.modal id="create-expense" title="Tambah Catatan Pengeluaran Baru" max-width="md">
        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <x-form.input name="date" label="Tanggal Pengeluaran" type="date" :value="now()->toDateString()" required />
            
            <x-form.select name="category" label="Kategori Pengeluaran" required>
                <option value="Operasional">Operasional</option>
                <option value="Gaji & Honor">Gaji & Honor</option>
                <option value="Sewa & Utilitas">Sewa & Utilitas</option>
                <option value="Peralatan & Perlengkapan">Peralatan & Perlengkapan</option>
                <option value="Marketing & Iklan">Marketing & Iklan</option>
                <option value="Modal Proyek">Modal Proyek</option>
                <option value="Lain-lain">Lain-lain</option>
            </x-form.select>

            <x-form.input name="amount" label="Nominal (Rp)" type="number" step="0.01" min="0" placeholder="Contoh: 250000" required />
            
            <x-form.select name="invoice_id" label="Hubungkan ke Invoice (Opsional)">
                <option value="">— Tidak Terhubung ke Invoice Spesifik —</option>
                @foreach ($invoices as $inv)
                    <option value="{{ $inv->id }}">{{ $inv->number }} — {{ $inv->client?->name }} (Rp {{ number_format((float) $inv->total, 0, ',', '.') }})</option>
                @endforeach
            </x-form.select>

            <x-form.textarea name="description" label="Keterangan / Catatan" placeholder="Catatan singkat pengeluaran..." rows="2" />
            
            <x-form.input name="receipt" label="Unggah Bukti Kuitansi/Nota (Opsional)" type="file" />
            <p class="text-[11px] text-gray-500">Format: JPG, PNG, PDF (Maks 5MB)</p>

            <div class="flex justify-end gap-2 pt-2">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'create-expense')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan Pengeluaran</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableEl = document.getElementById('expenses-table');
        if (!tableEl) return;

        const ajaxUrl = tableEl.dataset.ajaxUrl;
        
        function loadExpenses() {
            const category = document.getElementById('filter-category').value;
            fetch(`${ajaxUrl}&category=${encodeURIComponent(category)}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = tableEl.querySelector('tbody');
                    if (!data.data || data.data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada data pengeluaran.</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = data.data.map(item => `
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium whitespace-nowrap">${item.date}</td>
                            <td class="px-4 py-3 whitespace-nowrap">${item.category}</td>
                            <td class="px-4 py-3">${item.description}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">${item.amount}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">${item.receipt}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">${item.action}</td>
                        </tr>
                    `).join('');
                });
        }

        document.getElementById('filter-category').addEventListener('change', loadExpenses);
        loadExpenses();
    });
</script>
@endpush
@endsection

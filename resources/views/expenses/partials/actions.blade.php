<div class="flex items-center justify-end gap-2" x-data="{ openEditModal: false, openDeleteModal: false }">
    <button type="button" @click="$dispatch('open-modal', 'edit-expense-{{ $expense->id }}')" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10 dark:hover:text-white" title="Edit">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
    </button>

    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded p-1 text-red-500 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300" title="Hapus">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </form>

    <!-- Modal Edit Expense -->
    <x-ui.modal id="edit-expense-{{ $expense->id }}" title="Edit Pengeluaran" max-width="md">
        <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <x-form.input name="date" label="Tanggal Pengeluaran" type="date" :value="$expense->date?->format('Y-m-d')" required />
            
            <x-form.select name="category" label="Kategori" required>
                @foreach (['Operasional', 'Gaji & Honor', 'Sewa & Utilitas', 'Peralatan & Perlengkapan', 'Marketing & Iklan', 'Modal Proyek', 'Lain-lain'] as $cat)
                    <option value="{{ $cat }}" @selected($expense->category === $cat)>{{ $cat }}</option>
                @endforeach
            </x-form.select>

            <x-form.input name="amount" label="Nominal (Rp)" type="number" step="0.01" min="0" :value="$expense->amount" required />
            <x-form.textarea name="description" label="Keterangan / Deskripsi" :value="$expense->description" rows="2" />
            
            <x-form.input name="receipt" label="Unggah Bukti Nota/Kuitansi Baru (Opsional)" type="file" />
            <p class="text-[11px] text-gray-500">Format: JPG, PNG, PDF (Maks 5MB)</p>

            <div class="flex justify-end gap-2 pt-2">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'edit-expense-{{ $expense->id }}')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>

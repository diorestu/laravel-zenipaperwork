<form method="POST" action="{{ $action }}" class="space-y-3">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @php
        $bankOptions = [
            'Bank Mandiri' => 'Bank Mandiri',
            'Bank Rakyat Indonesia' => 'Bank Rakyat Indonesia (BRI)',
            'Bank Central Asia' => 'Bank Central Asia (BCA)',
            'Bank Negara Indonesia' => 'Bank Negara Indonesia (BNI)',
            'Bank Tabungan Negara' => 'Bank Tabungan Negara (BTN)',
            'Bank Syariah Indonesia' => 'Bank Syariah Indonesia (BSI)',
            'Bank CIMB Niaga' => 'Bank CIMB Niaga',
            'Bank OCBC Indonesia' => 'Bank OCBC Indonesia',
            'Permata Bank' => 'Permata Bank',
            'Bank Danamon' => 'Bank Danamon',
        ];
    @endphp

    <x-form.select name="bank_name" label="Nama Bank" :options="$bankOptions" :value="$account?->bank_name">
        <option value="">Pilih bank</option>
    </x-form.select>
    <x-form.input name="account_name" label="Nama Rekening" :value="$account?->account_name" />
    <x-form.input name="account_number" label="Nomor Rekening" :value="$account?->account_number" />
    <x-form.input name="branch" label="Cabang" :value="$account?->branch" />
    <x-form.input name="currency" label="Mata Uang" :value="$account?->currency ?? 'IDR'" />
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary', $account?->is_primary)) class="rounded border-gray-300">
        Rekening utama
    </label>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account?->is_active ?? true)) class="rounded border-gray-300">
        Aktif
    </label>
    <label class="block">
        <span class="mb-1 block text-sm font-medium text-gray-700">Catatan</span>
        <textarea name="notes" rows="3" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none">{{ old('notes', $account?->notes) }}</textarea>
    </label>
    <div class="flex justify-end">
        <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Simpan</button>
    </div>
</form>

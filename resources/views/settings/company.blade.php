@extends('layouts.app')

@section('content')
<div class="max-w-4xl space-y-6">
    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Profil Perusahaan</h1>
        <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" class="mt-4 grid gap-4">
            @csrf
            @method('PUT')
            <div
                x-data="{
                    preview: @js($company?->logo_path ? asset('storage/'.$company->logo_path) : null),
                    fileName: '',
                    choose(event) {
                        const [file] = event.target.files || [];
                        if (!file) return;
                        this.fileName = file.name;
                        this.preview = URL.createObjectURL(file);
                    }
                }"
            >
                <div class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                    <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Logo Perusahaan</span>
                    <div>
                        <label class="flex cursor-pointer items-center gap-4 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-gray-700 dark:bg-gray-900 dark:hover:border-brand-500/50 dark:hover:bg-brand-500/10">
                            <span class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-white/[0.03]">
                                <template x-if="preview">
                                    <img :src="preview" alt="Pratinjau logo perusahaan" class="h-full w-full object-contain p-2">
                                </template>
                                <template x-if="!preview">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-gray-400" aria-hidden="true">
                                        <path d="M4 16.5V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V16.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                        <path d="M12 4V15M12 4L8 8M12 4L16 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </template>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white/90">Unggah logo</span>
                                <span class="mt-1 block truncate text-xs text-gray-500 dark:text-gray-400" x-text="fileName || 'PNG, JPG, atau SVG'"></span>
                            </span>
                            <input name="logo" type="file" accept="image/*" class="sr-only" @change="choose">
                        </label>
                        @error('logo')<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <x-form.input name="name" label="Nama" :value="$company?->name" />
            <x-form.input name="email" label="Email" :value="$company?->email" />
            <x-form.input name="phone" label="Telepon" :value="$company?->phone" />
            <x-form.input name="pic_name" label="Nama PIC" :value="$company?->pic_name" />
            <x-form.input name="pic_email" label="PIC Email" type="email" :value="$company?->pic_email" />
            <x-form.input name="pic_phone" label="Telepon PIC" :value="$company?->pic_phone" />
            <x-form.input name="tax_number" label="Nomor Pajak" :value="$company?->tax_number" />
            <label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</span>
                <span class="block">
                    <textarea name="address" rows="3" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500">{{ old('address', $company?->address) }}</textarea>
                    @error('address')<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
                </span>
            </label>

            <!-- Section Customize Template Numbering Invoice -->
            <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-800"
                 x-data="{
                    prefix: @js(old('invoice_number_prefix', $company?->invoice_number_prefix ?? 'INV')),
                    format: @js(old('invoice_number_format', $company?->invoice_number_format ?? '{PREFIX}/{YYYY}/{MM}/{NUMBER}')),
                    padding: @js((int) old('invoice_number_padding', $company?->invoice_number_padding ?? 4)),
                    nextNumber: @js((int) old('invoice_next_number', $company?->invoice_next_number ?? 1)),
                    get previewNumber() {
                        let num = String(this.nextNumber || 1).padStart(parseInt(this.padding) || 4, '0');
                        let now = new Date();
                        let yyyy = now.getFullYear();
                        let yy = String(yyyy).slice(-2);
                        let mm = String(now.getMonth() + 1).padStart(2, '0');
                        let dd = String(now.getDate()).padStart(2, '0');
                        let romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                        let roman = romanMonths[now.getMonth()];
                        
                        let res = this.format || '{PREFIX}/{YYYY}/{MM}/{NUMBER}';
                        res = res.replaceAll('{PREFIX}', this.prefix || 'INV');
                        res = res.replaceAll('{YYYY}', yyyy);
                        res = res.replaceAll('{YY}', yy);
                        res = res.replaceAll('{MM}', mm);
                        res = res.replaceAll('{DD}', dd);
                        res = res.replaceAll('{NUMBER}', num);
                        res = res.replaceAll('{NUM}', num);
                        res = res.replaceAll('{ROMAN}', roman);
                        res = res.replaceAll('{ROMAN_MONTH}', roman);
                        return res;
                    }
                 }"
            >
                <div class="mb-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">Format & Penomoran Invoice</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kustomisasi penomoran otomatis invoice perusahaan Anda menggunakan tag dinamis.</p>
                </div>

                <div class="rounded-xl border border-brand-100 bg-brand-50/50 p-4 dark:border-brand-500/20 dark:bg-brand-500/10 mb-4">
                    <span class="text-xs font-semibold text-brand-700 dark:text-brand-300 block mb-1">🔍 Pratinjau Nomor Invoice Berikutnya:</span>
                    <span class="text-lg font-bold font-mono text-brand-600 dark:text-brand-400" x-text="previewNumber"></span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Prefix Invoice</label>
                        <input type="text" name="invoice_number_prefix" x-model="prefix" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="misal: INV, FAKTUR">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Digit Pading Nomor Urut</label>
                        <select name="invoice_number_padding" x-model="padding" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="3">3 Digit (001, 002)</option>
                            <option value="4">4 Digit (0001, 0002)</option>
                            <option value="5">5 Digit (00001, 00002)</option>
                            <option value="6">6 Digit (000001, 000002)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Format Template Penomoran</label>
                        <input type="text" name="invoice_number_format" x-model="format" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 font-mono focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="{PREFIX}/{YYYY}/{MM}/{NUMBER}">
                        
                        <!-- Pilihan Template Cepat -->
                        <div class="mt-2 flex flex-wrap gap-1.5 text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400">Template Cepat:</span>
                            <button type="button" @click="format = '{PREFIX}/{YYYY}/{MM}/{NUMBER}'" class="rounded border border-gray-200 px-2 py-0.5 font-mono text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{PREFIX}/{YYYY}/{MM}/{NUMBER}</button>
                            <button type="button" @click="format = '{PREFIX}-{YYYY}{MM}-{NUMBER}'" class="rounded border border-gray-200 px-2 py-0.5 font-mono text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{PREFIX}-{YYYY}{MM}-{NUMBER}</button>
                            <button type="button" @click="format = '{PREFIX}/{ROMAN}/{YYYY}/{NUMBER}'" class="rounded border border-gray-200 px-2 py-0.5 font-mono text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{PREFIX}/{ROMAN}/{YYYY}/{NUMBER}</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nomor Urut Berikutnya</label>
                        <input type="number" name="invoice_next_number" x-model="nextNumber" min="1" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="mt-3 rounded-lg bg-gray-50 p-3 text-[11px] text-gray-500 dark:bg-gray-900/50 dark:text-gray-400 leading-relaxed">
                    <strong>Tag Dinamis Tersedia:</strong> <code class="text-brand-600 dark:text-brand-400">{PREFIX}</code> (Prefix), <code class="text-brand-600 dark:text-brand-400">{YYYY}</code> (Tahun 4 digit), <code class="text-brand-600 dark:text-brand-400">{YY}</code> (Tahun 2 digit), <code class="text-brand-600 dark:text-brand-400">{MM}</code> (Bulan 2 digit), <code class="text-brand-600 dark:text-brand-400">{DD}</code> (Tanggal), <code class="text-brand-600 dark:text-brand-400">{ROMAN}</code> (Bulan Romawi: I..XII), <code class="text-brand-600 dark:text-brand-400">{NUMBER}</code> (Nomor Urut Padded).
                </div>
            </div>

            <div class="grid gap-2 sm:grid-cols-[30%_1fr] mt-4">
                <span class="hidden sm:block"></span>
                <div class="flex justify-end">
                    <button class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection

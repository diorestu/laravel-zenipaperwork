@extends('layouts.app')

@section('content')
<section class="max-w-4xl rounded-lg border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
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
        <div class="grid gap-2 sm:grid-cols-[30%_1fr]">
            <span class="hidden sm:block"></span>
            <div class="flex justify-end">
                <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Simpan</button>
            </div>
        </div>
    </form>
</section>
@endsection

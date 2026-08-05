@extends('layouts.fullscreen-layout')

@section('content')
<div class="min-h-screen bg-[#F7F6F3] pb-8 text-gray-900 dark:bg-gray-950 dark:text-white">
    <header class="sticky top-0 z-40 border-b border-gray-200/80 bg-white/95 px-4 py-4 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-900/95">
        <div class="mx-auto flex max-w-md items-center justify-between">
            <a href="{{ route('mobile.app') }}" aria-label="Kembali" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <x-heroicon-o-chevron-left class="h-5 w-5" />
            </a>
            <div class="text-center">
                <h1 class="text-xs font-bold text-gray-900 dark:text-white">Edit Profil Perusahaan</h1>
                <p class="text-[10px] text-gray-500 dark:text-gray-400">Kelola data profil untuk mobile app</p>
            </div>
            <span class="w-9"></span>
        </div>
    </header>

    <main class="mx-auto max-w-md space-y-4 px-4 pt-4">
        <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
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
                class="space-y-4"
            >
                <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="from_mobile" value="1">

                    <div class="rounded-2xl border border-brand-100 bg-brand-50/60 p-4 dark:border-brand-500/20 dark:bg-brand-500/10">
                        <div class="flex items-center gap-4">
                            <label class="flex h-16 w-16 shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                                <template x-if="preview">
                                    <img :src="preview" alt="Pratinjau logo" class="h-full w-full object-contain p-2">
                                </template>
                                <template x-if="!preview">
                                    <x-heroicon-o-photo class="h-7 w-7 text-gray-400" />
                                </template>
                                <input name="logo" type="file" accept="image/*" class="sr-only" @change="choose">
                            </label>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Logo perusahaan</p>
                                <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400" x-text="fileName || 'PNG, JPG, atau SVG'"></p>
                            </div>
                        </div>
                        @error('logo')<span class="mt-2 block text-xs text-error-600">{{ $message }}</span>@enderror
                    </div>

                    <div class="space-y-3">
                        <x-form.input name="name" label="Nama Perusahaan" :value="$company?->name" placeholder="Nama perusahaan" />
                        <x-form.input name="email" label="Email Perusahaan" :value="$company?->email" placeholder="nama@perusahaan.com" />
                        <x-form.input name="phone" label="Telepon Perusahaan" :value="$company?->phone" placeholder="08xxxxxxxxxx" />
                        <x-form.input name="pic_name" label="Nama PIC" :value="$company?->pic_name" placeholder="Nama PIC" />
                        <x-form.input name="pic_email" label="Email PIC" type="email" :value="$company?->pic_email" placeholder="pic@perusahaan.com" />
                        <x-form.input name="pic_phone" label="Telepon PIC" :value="$company?->pic_phone" placeholder="08xxxxxxxxxx" />
                        <x-form.input name="tax_number" label="Nomor Pajak" :value="$company?->tax_number" placeholder="NPWP / nomor pajak" />
                    </div>

                    <div class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
                        <label class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                        <div>
                            <textarea name="address" rows="4" placeholder="Alamat perusahaan" class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white">{{ old('address', $company?->address) }}</textarea>
                            @error('address')<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="sticky bottom-4 pt-2">
                        <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-brand-500 px-4 text-sm font-semibold text-white shadow-theme-xs transition hover:bg-brand-600 active:scale-[0.98]">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @include('settings.partials.settings-nav')

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Kelola & Impor Data</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Ekspor cadangan data akun atau impor data JSON dari file cadangan Paperwork.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/30 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-medium text-rose-800 dark:border-rose-800/40 dark:bg-rose-950/30 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Import Card -->
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Impor Data JSON</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Unggah file .json cadangan untuk diimpor ke akun Anda</p>
                </div>
            </div>

            <form action="{{ route('settings.data.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih File JSON Backup</label>
                    <input type="file" name="json_file" accept=".json,application/json,text/plain" required
                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/20 dark:file:text-brand-300 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 cursor-pointer">
                    @error('json_file')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-lg bg-amber-50 p-3 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30">
                    <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed">
                        <strong>Catatan:</strong> Impor data akan menambah atau memperbarui Produk, Klien, Penawaran, dan Invoice yang ada tanpa menghapus data aktif lainnya.
                    </p>
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Impor Data Sekarang
                </button>
            </form>
        </section>

        <!-- Export Card -->
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Ekspor Data JSON</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unduh seluruh cadangan data akun Anda dalam format JSON</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    Unduh salinan cadangan lengkap yang mencakup data Produk, Klien, Penawaran (Quotation), dan Invoice. File JSON ini dapat disimpan secara lokal sebagai cadangan atau diimpor kembali di kemudian hari.
                </p>
            </div>

            <a href="{{ route('settings.data.export') }}" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Unduh File Backup JSON
            </a>
        </section>
    </div>
    <div class="mt-8 border-t border-gray-200 dark:border-gray-800 pt-8">
        <section class="rounded-2xl border border-rose-200 bg-white p-6 shadow-theme-xs dark:border-rose-900/30 dark:bg-rose-950/10">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Reset Ruang Kerja (Milik Sendiri)</h2>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400 leading-relaxed max-w-3xl">
                        Aksi ini akan <strong>menghapus seluruh data transaksi dan master secara permanen</strong> pada ruang kerja Anda saat ini (Invoice, Penawaran, Klien, Produk, Pengeluaran, Akun Bank). Data ini hanya milik Anda dan tidak memengaruhi pengguna lain. <strong>Tindakan ini tidak dapat dibatalkan.</strong>
                    </p>
                    <form action="{{ route('settings.data.reset') }}" method="POST" class="mt-4 inline-block" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus seluruh data pada ruang kerja ini secara permanen? Data yang telah dihapus tidak dapat dikembalikan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-semibold text-white shadow-theme-xs hover:bg-rose-700 transition focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            Ya, Hapus Semua Data Milik Saya
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

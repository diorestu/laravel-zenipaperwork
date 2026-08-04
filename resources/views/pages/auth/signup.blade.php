@extends('layouts.fullscreen-layout')

@section('content')
    <main class="min-h-screen bg-[#F7F6F3] text-[#111111] dark:bg-gray-950 dark:text-white">
        <div class="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 py-8 sm:px-8 lg:px-10">
            <header class="flex items-center justify-between">
                <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                    <img src="{{ asset('images/logo/paperwork-logo.png') }}" alt="Paperwork Logo" class="h-8 w-auto">
                </a>

                <a href="{{ route('signin') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Masuk
                </a>
            </header>

            <section class="flex flex-1 items-center justify-center py-12">
                <div class="grid w-full items-center gap-10 lg:grid-cols-[0.9fr_1fr]">
                    <div class="hidden max-w-md lg:block">
                        <p class="mb-4 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                            Workspace baru
                        </p>
                        <h1 class="text-5xl font-semibold leading-[1.05] tracking-tight text-gray-950 dark:text-white">
                            Mulai dengan data perusahaan yang rapi.
                        </h1>
                        <p class="mt-5 text-base leading-7 text-gray-600 dark:text-gray-400">
                            Buat akun owner untuk menyiapkan klien, produk, dokumen penawaran, invoice, dan pembayaran.
                        </p>
                    </div>

                    <div class="mx-auto w-full max-w-[480px] rounded-xl border border-[#EAEAEA] bg-white p-6 dark:border-white/10 dark:bg-white/[0.03] sm:p-8">
                        <div class="mb-8">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">
                                Daftar
                            </p>
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                Buat akun Paperwork
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Satu workspace untuk perusahaan dan pemilik akun.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                            @csrf

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="company_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Perusahaan</label>
                                    <input
                                        id="company_name"
                                        type="text"
                                        name="company_name"
                                        value="{{ old('company_name') }}"
                                        autocomplete="organization"
                                        autofocus
                                        placeholder="Konsulin Studio"
                                        class="h-11 w-full rounded-md border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                                    />
                                    @error('company_name')
                                        <p class="mt-1.5 text-xs text-error-600 dark:text-error-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama owner</label>
                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        autocomplete="name"
                                        placeholder="Nama lengkap"
                                        class="h-11 w-full rounded-md border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                                    />
                                    @error('name')
                                        <p class="mt-1.5 text-xs text-error-600 dark:text-error-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    placeholder="nama@perusahaan.com"
                                    class="h-11 w-full rounded-md border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                                />
                                @error('email')
                                    <p class="mt-1.5 text-xs text-error-600 dark:text-error-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kata Sandi</label>
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="Minimal 8 karakter"
                                        class="h-11 w-full rounded-md border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                                    />
                                    @error('password')
                                        <p class="mt-1.5 text-xs text-error-600 dark:text-error-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi</label>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="Ulangi kata sandi"
                                        class="h-11 w-full rounded-md border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                                    />
                                </div>
                            </div>

                            <button class="flex h-11 w-full items-center justify-center rounded-md bg-[#111111] px-4 text-sm font-semibold text-white transition hover:bg-[#333333] active:scale-[0.99] dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">
                                Buat akun
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Sudah punya akun?
                            <a href="{{ route('signin') }}" class="font-semibold text-gray-900 hover:text-gray-600 dark:text-white dark:hover:text-gray-300">Masuk</a>
                        </p>

                        <div class="mt-8 border-t border-[#EAEAEA] pt-4 text-center text-xs text-gray-400 dark:border-white/10 dark:text-gray-500">
                            Dengan mendaftar, Anda menyetujui <a href="{{ route('terms-of-service') }}" class="underline hover:text-gray-600 dark:hover:text-gray-400">Ketentuan Pelanggan</a> dan <a href="{{ route('privacy-policy') }}" class="underline hover:text-gray-600 dark:hover:text-gray-400">Kebijakan Privasi</a> kami.
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection

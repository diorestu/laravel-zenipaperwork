@extends('layouts.fullscreen-layout')

@section('content')
    <main class="flex min-h-screen items-center justify-center bg-[#F7F6F3] text-[#111111] dark:bg-gray-950 dark:text-white">
        <div class="w-full max-w-[440px] px-4 py-8">
            <!-- Header Logo (Centered) -->
            <div class="mb-6 flex justify-center">
                <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                    <img src="{{ asset('images/logo/paperwork-logo.png') }}" alt="Paperwork Logo" class="h-8 w-auto">
                </a>
            </div>

            <!-- Main login card -->
            <div class="rounded-xl border border-[#EAEAEA] bg-white p-6 shadow-xs dark:border-white/10 dark:bg-white/[0.03] sm:p-8">
                <div class="mb-8">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">
                        Masuk
                    </p>
                    <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        Selamat datang kembali
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Gunakan email dan password yang terdaftar.
                    </p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                            placeholder="nama@perusahaan.com"
                            class="h-11 w-full rounded-md border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                        />
                        @error('email')
                            <p class="mt-1.5 text-xs text-error-600 dark:text-error-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                Lupa password?
                            </a>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="Masukkan password"
                            class="h-11 w-full rounded-md border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                        />
                        @error('password')
                            <p class="mt-1.5 text-xs text-error-600 dark:text-error-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 dark:border-white/20 dark:bg-transparent">
                        Ingat sesi saya
                    </label>

                    <button class="flex h-11 w-full items-center justify-center rounded-md bg-[#111111] px-4 text-sm font-semibold text-white transition hover:bg-[#333333] active:scale-[0.99] dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">
                        Masuk
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum punya akun?
                    <a href="{{ route('signup') }}" class="font-semibold text-gray-900 hover:text-gray-600 dark:text-white dark:hover:text-gray-300">Daftar sekarang</a>
                </p>
            </div>

            <!-- Footer links under the card -->
            <div class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
                Dengan masuk, Anda menyetujui <a href="{{ route('terms-of-service') }}" class="underline hover:text-gray-600 dark:hover:text-gray-400">Ketentuan Pelanggan</a> dan <a href="{{ route('privacy-policy') }}" class="underline hover:text-gray-600 dark:hover:text-gray-400">Kebijakan Privasi</a> kami.
            </div>
        </div>
    </main>
@endsection

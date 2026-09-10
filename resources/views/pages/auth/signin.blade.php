@extends('layouts.fullscreen-layout')

@section('content')
    <main class="flex min-h-screen items-center justify-center bg-[#F7F6F3] text-[#111111] dark:bg-gray-950 dark:text-white">
        <div class="w-full max-w-[440px] px-4 py-8">
            <!-- Header Logo (Centered) -->
            <div class="mb-6 flex justify-center">
                <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                    <img src="{{ asset('images/logo/paperwork-logo.png') }}" alt="Paperwork Logo" class="h-8 w-auto dark:hidden">
                    <img src="{{ asset('img/logo/logo_white.png') }}" alt="Paperwork Logo" class="hidden h-8 w-auto dark:block">
                </a>
            </div>

            <!-- Main login card -->
            <div class="rounded-xl border border-[#EAEAEA] bg-white p-6 shadow-xs dark:border-white/10 dark:bg-white/[0.03] sm:p-8">
                <div class="mb-8 text-center sm:text-left">
                    <div class="mb-3 flex items-center justify-center sm:justify-start gap-2">
                        <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            paperwork.biz.id
                        </span>
                    </div>
                    <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        Selamat datang kembali
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Masuk ke akun resmi Paperwork untuk mengelola invoice dan pembayaran.
                    </p>
                </div>

                <a href="{{ route('auth.google.redirect') }}" class="mb-5 flex h-11 w-full items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-white/10 dark:bg-transparent dark:text-white dark:hover:bg-white/[0.04]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.3 9.14 5.38 12 5.38z"/>
                    </svg>
                    Masuk dengan Google
                </a>

                <div class="mb-5 flex items-center gap-3">
                    <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
                    <span class="text-xs font-medium text-gray-400">atau</span>
                    <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
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

                    <div x-data="{ showPassword: false }">
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kata Sandi</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                Lupa kata sandi?
                            </a>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                class="h-11 w-full rounded-md border border-gray-200 bg-white pl-3.5 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 dark:border-white/10 dark:bg-transparent dark:text-white dark:focus:border-white"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                                tabindex="-1"
                            >
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
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
            <div class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500 space-y-1">
                <p>Dengan masuk, Anda menyetujui <a href="{{ route('terms-of-service') }}" class="underline hover:text-gray-600 dark:hover:text-gray-400">Ketentuan Pelanggan</a> dan <a href="{{ route('privacy-policy') }}" class="underline hover:text-gray-600 dark:hover:text-gray-400">Kebijakan Privasi</a> kami.</p>
                <p class="text-[11px] text-gray-400 opacity-80">© {{ date('Y') }} Paperwork (paperwork.biz.id). Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </main>
@endsection

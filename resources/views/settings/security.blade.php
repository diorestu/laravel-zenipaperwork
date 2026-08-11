@extends('layouts.app')

@section('content')
<div class="max-w-4xl space-y-6">
    @include('settings.partials.settings-nav')

    <!-- Informasi Akun & Keamanan -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-5 dark:border-gray-800">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xl font-bold text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    <div class="mt-1 flex items-center gap-2">
                        @if ($user->email_verified_at)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Email Terverifikasi
                            </span>
                        @endif

                        @if ($user->google_id)
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                Google SSO Connected
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 sm:text-right">
                <span>Peran: <strong class="text-gray-900 dark:text-white uppercase">{{ $user->role ?? 'User' }}</strong></span>
                <span class="block text-[11px] text-gray-400">Terdaftar sejak {{ $user->created_at?->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Form Ubah Kata Sandi -->
        <div class="mt-6">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ubah Kata Sandi</h3>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pastikan kata sandi Anda menggunakan minimal 8 karakter dengan kombinasi huruf dan angka.</p>

            <form method="POST" action="{{ route('settings.security.password') }}" class="mt-4 space-y-4 max-w-lg">
                @csrf
                @method('PUT')

                @if ($user->password)
                    <x-form.input name="current_password" label="Kata Sandi Saat Ini" type="password" required placeholder="Masukkan kata sandi saat ini" />
                @endif

                <x-form.input name="password" label="Kata Sandi Baru" type="password" required placeholder="Minimal 8 karakter" />
                <x-form.input name="password_confirmation" label="Konfirmasi Kata Sandi Baru" type="password" required placeholder="Ulangi kata sandi baru" />

                <div class="pt-2">
                    <x-ui.button type="submit" variant="primary">
                        Perbarui Kata Sandi
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>

    <!-- Perangkat & Akses Sesi Aktif -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Akses Perangkat & Token Aplikasi (API Tokens)</h3>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Daftar token dan sesi aplikasi mobile yang memiliki izin mengakses akun Anda.</p>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 uppercase text-gray-700 dark:bg-white/5 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2.5">Nama Perangkat / Token</th>
                        <th class="px-4 py-2.5">Terakhir Digunakan</th>
                        <th class="px-4 py-2.5">Tanggal Dibuat</th>
                        <th class="px-4 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($tokens as $token)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                {{ $token->name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Belum pernah digunakan' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $token->created_at?->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('settings.security.tokens.revoke', $token->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin mencabut akses perangkat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:underline dark:text-red-400">
                                        Cabut Akses
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                Tidak ada akses token / perangkat eksternal yang aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

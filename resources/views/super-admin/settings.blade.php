@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Title -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pengaturan Payment Gateway Superadmin</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Pilih provider Payment Gateway aktif (Pakasir / Sumopod) dan atur kredensial API key.</p>
        </div>
        <div>
            <a href="{{ route('super-admin.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('super-admin.settings.update') }}" method="POST" class="space-y-6" x-data="{ activeGateway: '{{ old('active_payment_gateway', $activeGateway) }}' }">
        @csrf
        @method('PUT')

        <!-- Provider Selection Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/60">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-1">Pilih Payment Gateway Aktif</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Layanan QRIS otomatis yang digunakan saat pengguna melakukan checkout atau upgrade billing.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Option Pakasir -->
                <label @click="activeGateway = 'pakasir'"
                       class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none transition-all dark:border-gray-800 dark:bg-gray-800/40"
                       :class="activeGateway === 'pakasir' ? 'border-brand-600 bg-brand-50/50 ring-2 ring-brand-500 dark:bg-brand-500/10' : 'border-gray-200 bg-white hover:border-gray-300'">
                    <input type="radio" name="active_payment_gateway" value="pakasir" class="sr-only" x-model="activeGateway" {{ old('active_payment_gateway', $activeGateway) === 'pakasir' ? 'checked' : '' }}>
                    <div class="flex w-full items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold">
                                QR
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-gray-900 dark:text-white">Pakasir</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Payment Gateway QRIS Auto Verification</span>
                            </div>
                        </div>
                        <div x-show="activeGateway === 'pakasir'" class="text-brand-600 dark:text-brand-400 font-bold text-xs flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Terpilih</span>
                        </div>
                    </div>
                </label>

                <!-- Option Sumopod -->
                <label @click="activeGateway = 'sumopod'"
                       class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none transition-all dark:border-gray-800 dark:bg-gray-800/40"
                       :class="activeGateway === 'sumopod' ? 'border-brand-600 bg-brand-50/50 ring-2 ring-brand-500 dark:bg-brand-500/10' : 'border-gray-200 bg-white hover:border-gray-300'">
                    <input type="radio" name="active_payment_gateway" value="sumopod" class="sr-only" x-model="activeGateway" {{ old('active_payment_gateway', $activeGateway) === 'sumopod' ? 'checked' : '' }}>
                    <div class="flex w-full items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 font-bold">
                                SP
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-gray-900 dark:text-white">Sumopod</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Sumopod Payment Gateway API</span>
                            </div>
                        </div>
                        <div x-show="activeGateway === 'sumopod'" class="text-brand-600 dark:text-brand-400 font-bold text-xs flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Terpilih</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Sumopod Credentials Card (Shown Realtime when Sumopod Selected) -->
        <div x-show="activeGateway === 'sumopod'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/60 space-y-4">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 text-xs font-bold dark:bg-emerald-500/20 dark:text-emerald-300">SP</span>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Konfigurasi Sumopod</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Atur Endpoint API Sandbox/Production dan X-Api-Key Sumopod Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sumopod Base URL API</label>
                    <input type="text" name="sumopod_base_url" value="{{ old('sumopod_base_url', $sumopodBaseUrl) }}" placeholder="https://api-pay-sandbox.sumopod.com/api/v1" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                    <span class="text-[11px] text-gray-400 mt-1 block">Default: `https://api-pay-sandbox.sumopod.com/api/v1`</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sumopod X-Api-Key</label>
                    <input type="password" name="sumopod_api_key" value="{{ old('sumopod_api_key', $sumopodApiKey) }}" placeholder="Masukkan X-Api-Key Sumopod" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                    <span class="text-[11px] text-gray-400 mt-1 block">API Key resmi dari dashboard Sumopod.</span>
                </div>
            </div>

            <!-- Webhook Notice -->
            <div class="rounded-xl border border-sky-100 bg-sky-50/70 p-3.5 text-xs text-sky-900 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-300">
                <span class="font-bold block mb-1">Webhook URL Sumopod:</span>
                <code class="rounded bg-sky-100 dark:bg-sky-900/40 px-2 py-1 font-mono text-[11px] select-all">{{ url('/webhooks/sumopod') }}</code>
                <p class="mt-1 text-[11px] text-sky-700 dark:text-sky-400">Masukkan URL webhook di atas pada dashboard Sumopod Anda untuk auto-verifikasi pembayaran QRIS.</p>
            </div>
        </div>

        <!-- Pakasir Credentials Card (Shown Realtime when Pakasir Selected) -->
        <div x-show="activeGateway === 'pakasir'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/60 space-y-4">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold dark:bg-indigo-500/20 dark:text-indigo-300">QR</span>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Konfigurasi Pakasir</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Atur Project ID & API Key Pakasir Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pakasir Project Slug / ID</label>
                    <input type="text" name="pakasir_project" value="{{ old('pakasir_project', $pakasirProject) }}" placeholder="paperwork" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pakasir API Key</label>
                    <input type="password" name="pakasir_api_key" value="{{ old('pakasir_api_key', $pakasirApiKey) }}" placeholder="Masukkan API Key Pakasir" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <!-- Webhook Notice -->
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-3.5 text-xs text-indigo-900 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-300">
                <span class="font-bold block mb-1">Webhook URL Pakasir:</span>
                <code class="rounded bg-indigo-100 dark:bg-indigo-900/40 px-2 py-1 font-mono text-[11px] select-all">{{ url('/webhooks/pakasir') }}</code>
            </div>
        </div>

        <!-- Package Pricing Settings Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/60 space-y-4">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700 text-xs font-bold dark:bg-amber-500/20 dark:text-amber-300">Rp</span>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pengaturan Harga Paket Berlangganan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Atur harga langganan bulanan paket Starter, Business, dan Enterprise secara dinamis di database.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Paket Starter (Rp/bulan)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="plan_price_starter" value="{{ old('plan_price_starter', $priceStarter) }}" min="0" step="1000" class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Paket Business (Rp/bulan)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="plan_price_business" value="{{ old('plan_price_business', $priceBusiness) }}" min="0" step="1000" class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Paket Enterprise (Rp/bulan)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="plan_price_enterprise" value="{{ old('plan_price_enterprise', $priceEnterprise) }}" min="0" step="1000" class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
            </div>
            <p class="text-[11px] text-gray-400">Catatan: Diskon langganan tahunan (10%) akan dihitung secara otomatis berdasarkan nominal harga di atas.</p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-6 text-xs font-bold text-white shadow-theme-xs transition hover:bg-brand-700 active:scale-[0.98]">
                Simpan Seluruh Pengaturan Superadmin
            </button>
        </div>
    </form>
</div>
@endsection

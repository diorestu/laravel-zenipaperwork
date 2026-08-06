@extends('layouts.fullscreen-layout')

@section('content')
<div x-data="{ billingPeriod: 'monthly' }" class="min-h-screen bg-[#F7F6F3] pb-12 text-gray-900 dark:bg-gray-950 dark:text-white">
    <!-- Top Header App Bar -->
    <header class="sticky top-0 z-40 border-b border-gray-200/80 bg-white/95 px-4 py-4 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-900/95">
        <div class="mx-auto flex max-w-md items-center justify-between">
            <a href="{{ route('mobile.app') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">
                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Kembali
            </a>
            <h1 class="text-sm font-bold text-gray-900 dark:text-white">Paket Langganan</h1>
            <span class="w-12"></span>
        </div>
    </header>

    <!-- Main Mobile Content -->
    <main class="mx-auto max-w-md space-y-4 px-4 pt-4">
        <!-- Active Trial / Subscription Status Card -->
        @if ($onTrial)
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 to-indigo-700 p-5 text-white shadow-theme-md">
                <div class="flex items-center justify-between text-xs font-medium text-white/80">
                    <span>Masa Uji Coba Gratis</span>
                    <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold backdrop-blur-xs">30 Hari</span>
                </div>
                <h2 class="mt-2 text-xl font-extrabold text-white">Paket Business Aktif</h2>
                <p class="mt-1 text-xs text-white/80">
                    Berakhir {{ $trialEndsAt?->locale('id')->translatedFormat('d F Y') }} (sisa <strong class="text-amber-200">{{ $trialDaysRemaining }} hari</strong>).
                </p>
            </div>
        @endif

        <!-- Billing Period Toggle -->
        <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
            <span class="pl-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Periode:</span>
            <div class="inline-flex rounded-xl bg-gray-100 p-1 dark:bg-gray-800">
                <button type="button" @click="billingPeriod = 'monthly'" :class="billingPeriod === 'monthly' ? 'bg-brand-500 font-bold text-white shadow-theme-xs' : 'text-gray-600 dark:text-gray-400'" class="rounded-lg px-3 py-1.5 text-xs transition">
                    Bulanan
                </button>
                <button type="button" @click="billingPeriod = 'yearly'" :class="billingPeriod === 'yearly' ? 'bg-brand-500 font-bold text-white shadow-theme-xs' : 'text-gray-600 dark:text-gray-400'" class="rounded-lg px-3 py-1.5 text-xs transition">
                    Tahunan <span class="text-[10px] text-amber-200 font-bold">-10%</span>
                </button>
            </div>
        </div>

        <!-- Mobile Plans List -->
        <div class="space-y-4">
            @foreach ($plans as $plan)
                @php($yearlyAmount = (int) round($plan['amount'] * 12 * 0.9))
                @php($isActive = $activePlan === $plan['slug'])

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $plan['name'] }}</h3>
                        @if($isActive)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-extrabold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Paket Aktif</span>
                        @else
                            <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-[10px] font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">QRIS Instant</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $plan['description'] ?? 'Solusi kelola tagihan dan invoice usaha Anda' }}</p>

                    <!-- Price Box -->
                    <div class="mt-3.5 rounded-xl bg-gray-50 p-3 dark:bg-gray-800/50">
                        <div x-show="billingPeriod === 'monthly'" class="flex items-baseline gap-1">
                            <span class="text-xl font-extrabold text-gray-900 dark:text-white">Rp {{ number_format($plan['amount'], 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-500">/ bulan</span>
                        </div>
                        <div x-show="billingPeriod === 'yearly'" class="flex items-baseline gap-1" style="display: none;">
                            <span class="text-xl font-extrabold text-gray-900 dark:text-white">Rp {{ number_format($yearlyAmount, 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-500">/ tahun</span>
                        </div>
                    </div>

                    <!-- Features Checklist -->
                    <ul class="mt-3 space-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 fill-emerald-500" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Action Form -->
                    <form method="POST" action="{{ route('billing.store') }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="from_mobile" value="1">
                        <input type="hidden" name="package" value="{{ $plan['slug'] }}">
                        <input type="hidden" name="billing_period" :value="billingPeriod">
                        <input type="hidden" name="payment_method" value="qris">
                        <input type="hidden" name="amount" :value="billingPeriod === 'yearly' ? {{ $yearlyAmount }} : {{ $plan['amount'] }}">

                        <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-500 text-xs font-bold text-white shadow-theme-xs transition hover:bg-brand-600 active:scale-[0.98]">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                            Bayar Instant via QRIS
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Billing History List -->
        @if ($submissions->isNotEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 space-y-3">
                <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Riwayat Pembayaran Billing</h3>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($submissions as $sub)
                        <div class="flex items-center justify-between py-2.5">
                            <div>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ str($sub->package)->headline() }} ({{ $sub->billing_period }})</p>
                                <p class="text-[11px] text-gray-500">Rp {{ number_format($sub->amount, 0, ',', '.') }} • {{ $sub->created_at?->format('d M Y') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status-badge :status="$sub->status" />
                                @if($sub->payment_method === 'qris')
                                    <a href="{{ route('mobile.billing.show', $sub) }}" class="rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-[10px] font-semibold text-brand-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-brand-400">
                                        QRIS
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
</div>
@endsection

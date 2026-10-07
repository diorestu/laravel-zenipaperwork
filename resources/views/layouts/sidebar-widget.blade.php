@php
    $company = auth()->user()->company;
    $planSlug = $company?->getActivePlanSlug();
    $planName = match ($planSlug) {
        'trial' => 'Trial',
        'free' => 'Free',
        'basic', 'starter' => 'Basic',
        'plus', 'business' => 'Plus',
        'enterprise' => 'Enterprise',
        default => 'Expired',
    };
    $isActive = $planSlug !== null;
@endphp
<div class="mx-auto mb-5 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="mb-3 flex items-start justify-between gap-3">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Paket Saat Ini</p>
            <h3 class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                {{ $planName }}
            </h3>
        </div>
        @if($isActive)
            <span class="rounded-full bg-success-50 px-2 py-0.5 text-[11px] font-medium text-success-700 dark:bg-success-500/15 dark:text-success-400">Aktif</span>
        @else
            <span class="rounded-full bg-error-50 px-2 py-0.5 text-[11px] font-medium text-error-700 dark:bg-error-500/15 dark:text-error-400">Expired</span>
        @endif
    </div>
    <a href="{{ route('settings.billing') }}"
        class="flex items-center justify-center rounded-lg bg-brand-500 px-3 py-2 text-xs font-semibold text-white hover:bg-brand-600 transition">
        Kelola Paket
    </a>
    <a href="mailto:support@paperwork.biz.id?subject=Bantuan%20Layanan%20Paperwork"
        class="mt-2 flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-750 dark:hover:text-white transition"
        title="Hubungi CS via Email">
        <svg class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Bantuan Layanan CS
    </a>
</div>

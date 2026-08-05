@php
    $company = auth()->user()->company;
    $planSlug = $company?->getActivePlanSlug();
    $planName = match ($planSlug) {
        'trial' => 'Trial',
        'starter' => 'Starter',
        'business' => 'Business',
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
        class="flex items-center justify-center rounded-lg bg-brand-500 px-3 py-2 text-xs font-semibold text-white hover:bg-brand-600">
        Kelola Paket
    </a>
</div>

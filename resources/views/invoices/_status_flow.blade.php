@php
    $steps = [
        'draft' => [
            'label' => 'Draft',
            'desc' => 'Invoice dibuat',
            'icon' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
            'color' => 'brand'
        ],
        'sent' => [
            'label' => 'Terkirim',
            'desc' => 'Dikirim ke klien',
            'icon' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>',
            'color' => 'blue'
        ],
        'partial' => [
            'label' => 'Sebagian',
            'desc' => 'Terbayar sebagian',
            'icon' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'color' => 'amber'
        ],
        'paid' => [
            'label' => 'Lunas',
            'desc' => 'Lunas sepenuhnya',
            'icon' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'color' => 'success'
        ]
    ];
    $keys = array_keys($steps);
    $currentStatus = $invoice->status;

    // Handle special states like void or overdue
    $isBatal = $currentStatus === 'void';
    $isJatuhTempo = $invoice->is_overdue;

    // Determine current index in stepper
    $currentIndex = array_search($currentStatus, $keys, true);
    if ($currentIndex === false) {
        $currentIndex = 0; // fallback
    }
@endphp

<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-4 mb-6 dark:border-gray-800">
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Alur Status Invoice</h2>
            <p class="text-xs text-gray-500 mt-0.5">Status saat ini: 
                <span class="font-medium inline-flex items-center px-2 py-0.5 rounded-full text-xs
                    @if($isBatal) bg-gray-100 text-gray-800 dark:bg-white/5 dark:text-gray-300
                    @elseif($isJatuhTempo) bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400
                    @elseif($currentStatus === 'paid') bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                    @elseif($currentStatus === 'partial') bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400
                    @elseif($currentStatus === 'sent') bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                    @else bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400
                    @endif
                ">
                    {{ $isBatal ? 'Batal' : ($isJatuhTempo ? 'Jatuh Tempo' : str($currentStatus)->headline()) }}
                </span>
            </p>
        </div>

        @if($isBatal)
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-error-200 bg-error-50 text-error-700 text-xs font-semibold dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400 animate-pulse">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Invoice Dibatalkan (Batal)
            </div>
        @elseif($isJatuhTempo)
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-error-200 bg-error-50 text-error-700 text-xs font-semibold dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400 animate-pulse">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Melewati Tanggal Jatuh Tempo (Jatuh Tempo)
            </div>
        @endif
    </div>

    <!-- Stepper Container -->
    <div class="relative w-full px-4 sm:px-8">
        <!-- Connecting Line behind the steps -->
        <div class="absolute top-5 left-12 right-12 h-0.5 bg-gray-200 dark:bg-gray-800 -translate-y-1/2 z-0">
            <!-- Progress Line with Animation -->
            <div class="h-full bg-brand-500 transition-all duration-700 ease-out z-0" 
                 style="width: {{ $isBatal ? '0' : ($currentIndex / (count($keys) - 1) * 100) }}%">
            </div>
        </div>

        <!-- Steps list -->
        <div class="relative flex justify-between z-1">
            @foreach($steps as $name => $step)
                @php
                    $stepIndex = array_search($name, $keys, true);
                    $isCompleted = !$isBatal && $stepIndex <= $currentIndex;
                    $isActive = !$isBatal && $stepIndex === $currentIndex;
                @endphp
                <div class="flex flex-col items-center group">
                    <!-- Icon / Step circle with Animation & Colors -->
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 transition-all duration-500 
                        @if($isActive) 
                            border-brand-500 bg-brand-500 text-white shadow-md shadow-brand-500/20 scale-110 animate-bounce-short
                        @elseif($isCompleted) 
                            border-brand-500 bg-white text-brand-500 dark:bg-gray-900
                        @else 
                            border-gray-300 bg-white text-gray-400 dark:border-gray-800 dark:bg-gray-900 group-hover:border-gray-400 dark:group-hover:border-gray-700
                        @endif
                    ">
                        @if($isCompleted && !$isActive)
                            <!-- Check icon for completed steps -->
                            <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            {!! $step['icon'] !!}
                        @endif
                    </div>

                    <!-- Label and description -->
                    <div class="text-center mt-3 max-w-[120px]">
                        <p class="text-xs font-semibold transition-colors duration-300
                            @if($isActive) text-brand-600 dark:text-brand-400
                            @elseif($isCompleted) text-gray-900 dark:text-white
                            @else text-gray-400 dark:text-gray-600
                            @endif
                        ">
                            {{ $step['label'] }}
                        </p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 leading-snug">
                            {{ $step['desc'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
@keyframes bounce-short {
    0%, 100% {
        transform: translateY(0) scale(1.1);
    }
    50% {
        transform: translateY(-4px) scale(1.1);
    }
}
.animate-bounce-short {
    animation: bounce-short 2s infinite ease-in-out;
}
</style>

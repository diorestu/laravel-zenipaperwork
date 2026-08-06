@props(['document'])
@php
    $isQuotation = $document instanceof \App\Models\Quotation;
    $title = $isQuotation ? 'SURAT PENAWARAN' : 'INVOICE';
    $company = $document->company;
    $client = $document->client;
@endphp

<section class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
    <!-- Top Header: Logo & Company Info vs Doc Title & Metadata -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between border-b border-gray-100 pb-5 dark:border-gray-800">
        <!-- Company Logo & Details -->
        <div class="space-y-3">
            @if ($company->logo_path)
                <img src="{{ Storage::disk('public')->url($company->logo_path) }}" alt="{{ $company->name }}" class="h-12 w-auto object-contain">
            @else
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 font-bold text-gray-700 text-lg uppercase dark:bg-white/5 dark:text-gray-300">
                    {{ substr($company->name, 0, 2) }}
                </div>
            @endif

            <div class="text-xs space-y-0.5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $company->name }}</h3>
                @if ($company->address)<p class="text-gray-500 dark:text-gray-400 max-w-xs leading-relaxed">{{ $company->address }}</p>@endif
                @if ($company->email || $company->phone)
                    <p class="text-gray-400 dark:text-gray-500">
                        {{ $company->email }} {{ $company->email && $company->phone ? '|' : '' }} {{ $company->phone }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Document Title, Number & Dates -->
        <div class="sm:text-right space-y-2">
            <div class="space-y-0.5">
                <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h1>
                <p class="text-xs font-medium text-brand-600 dark:text-brand-400">{{ $document->number }}</p>
                <div class="inline-block mt-0.5">
                    <x-status-badge :status="$document->status" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-4 gap-y-0.5 text-xs sm:flex sm:flex-col sm:items-end">
                <div class="sm:flex sm:gap-2">
                    <span class="text-gray-400 dark:text-gray-500">Tanggal:</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300"><x-date-display :date="$document->issue_date" /></span>
                </div>
                @if ($isQuotation && $document->valid_until)
                    <div class="sm:flex sm:gap-2">
                        <span class="text-gray-400 dark:text-gray-500">Berlaku Hingga:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300"><x-date-display :date="$document->valid_until" /></span>
                    </div>
                @elseif (!$isQuotation && $document->due_date)
                    <div class="sm:flex sm:gap-2">
                        <span class="text-gray-400 dark:text-gray-500">Jatuh Tempo:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300"><x-date-display :date="$document->due_date" /></span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Client Info -->
    <div class="mt-5 border-b border-gray-100 pb-5 dark:border-gray-800">
        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">Ditujukan Kepada</p>
        <div class="text-xs space-y-0.5">
            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $client->name }}</h4>
            @if ($client->company_name)<p class="font-medium text-gray-700 dark:text-gray-300">{{ $client->company_name }}</p>@endif
            @if ($client->address)<p class="text-gray-500 dark:text-gray-400 max-w-sm leading-relaxed">{{ $client->address }}</p>@endif
            @if ($client->email || $client->phone)
                <p class="text-gray-400 dark:text-gray-500">
                    {{ $client->email }} {{ $client->email && $client->phone ? '|' : '' }} {{ $client->phone }}
                </p>
            @endif
        </div>
    </div>

    <!-- Item Lines Table -->
    <div class="mt-5">
        <x-document.item-lines :items="$document->items" :is-quotation="$isQuotation" />
    </div>

    <!-- Summary / Totals -->
    <div class="mt-5 flex flex-col sm:flex-row sm:justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <!-- Notes (if any) -->
        <div class="flex-1 max-w-md">
            @if ($document->notes)
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Catatan</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{!! nl2br(e($document->notes)) !!}</p>
            @endif
        </div>

        <!-- Totals breakdown -->
        <div class="w-full sm:w-60 space-y-2 text-xs">
            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                <span>Subtotal</span>
                <span class="font-medium text-gray-900 dark:text-white"><x-money :amount="$document->subtotal" /></span>
            </div>
            @if ($document->tax_rate > 0)
                <div class="flex justify-between text-gray-500 dark:text-gray-400">
                    <span>Pajak ({{ $document->tax_rate }}%)</span>
                    <span class="font-medium text-gray-900 dark:text-white"><x-money :amount="$document->tax_total" /></span>
                </div>
            @endif
            <div class="flex justify-between border-t border-gray-200 pt-2 text-sm font-bold text-gray-900 dark:text-white dark:border-gray-800">
                <span>Total</span>
                <span><x-money :amount="$document->total" /></span>
            </div>
        </div>
    </div>
</section>

<div class="flex items-center gap-3">
    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-sm font-semibold text-gray-700 dark:bg-white/5 dark:text-gray-300">
        {{ str($client->name)->substr(0, 1)->upper() }}
    </span>
    <div class="min-w-0">
        <p class="font-medium text-gray-900 dark:text-white/90">{{ $client->name }}</p>
        <p class="mt-0.5 max-w-[220px] truncate text-xs text-gray-500 dark:text-gray-400">{{ $client->company_name ?: 'Personal client' }}</p>
    </div>
</div>

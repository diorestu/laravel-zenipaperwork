<div class="flex items-center justify-center gap-1.5">
    <!-- View Detail Icon Button -->
    <a href="{{ route('invoices.show', $invoice) }}" 
       title="Lihat Detail Invoice" 
       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-50 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-brand-400" 
       aria-label="Lihat detail invoice">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M10 4.375C4.375 4.375 1.25 10 1.25 10C1.25 10 4.375 15.625 10 15.625C15.625 15.625 18.75 10 18.75 10C18.75 10 15.625 4.375 10 4.375Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <!-- Edit Icon Button -->
    <a href="{{ route('invoices.edit', $invoice) }}" 
       title="Edit Invoice" 
       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-50 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-brand-400" 
       aria-label="Edit invoice">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11.05 4.05L15.95 8.95M3.75 16.25L7.42 15.52C7.62 15.48 7.8 15.38 7.94 15.23L16.83 6.34C17.48 5.69 17.48 4.64 16.83 3.99L16.01 3.17C15.36 2.52 14.31 2.52 13.66 3.17L4.77 12.06C4.62 12.2 4.52 12.38 4.48 12.58L3.75 16.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>

    <!-- PDF Download Icon Button -->
    <a href="{{ route('invoices.pdf', $invoice) }}" 
       target="_blank" 
       title="Unduh PDF Invoice" 
       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-50 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-brand-400" 
       aria-label="Unduh PDF invoice">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.75 13.75V15C3.75 15.6904 4.30964 16.25 5 16.25H15C15.6904 16.25 16.25 15.6904 16.25 15V13.75M10 3.75V11.875M10 11.875L6.875 8.75M10 11.875L13.125 8.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <!-- Delete Form Icon Button -->
    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice {{ $invoice->number }}?')" class="inline-block">
        @csrf
        @method('DELETE')
        <button type="submit" 
                title="Hapus Invoice" 
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-error-500 hover:bg-error-50 hover:text-error-600 dark:text-error-400 dark:hover:bg-error-500/10" 
                aria-label="Hapus invoice">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7.5 2.5H12.5M3.75 5H16.25M14.58 5L14 15.05C13.95 15.88 13.26 16.53 12.43 16.53H7.57C6.74 16.53 6.05 15.88 6 15.05L5.42 5M8.33 8.33V13.33M11.67 8.33V13.33" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </form>
</div>

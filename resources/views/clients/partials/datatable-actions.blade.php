@php
    $record = [
        'id' => $client->id,
        'name' => $client->name,
        'company_name' => $client->company_name,
        'email' => $client->email,
        'phone' => $client->phone,
        'tax_number' => $client->tax_number,
        'address' => $client->address,
    ];
    $recordPayload = base64_encode(json_encode($record));
@endphp

<div class="flex items-center justify-center gap-1.5">
    <button type="button" class="js-edit-record inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-50 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-brand-400" data-modal="edit-client" data-record-payload="{{ $recordPayload }}" aria-label="Edit client">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11.05 4.05L15.95 8.95M3.75 16.25L7.42 15.52C7.62 15.48 7.8 15.38 7.94 15.23L16.83 6.34C17.48 5.69 17.48 4.64 16.83 3.99L16.01 3.17C15.36 2.52 14.31 2.52 13.66 3.17L4.77 12.06C4.62 12.2 4.52 12.38 4.48 12.58L3.75 16.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>
    <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Hapus client ini?')">
        @csrf
        @method('DELETE')
        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-error-500 hover:bg-error-50 hover:text-error-600 dark:text-error-400 dark:hover:bg-error-500/10" aria-label="Delete client">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7.5 2.5H12.5M3.75 5H16.25M14.58 5L14 15.05C13.95 15.88 13.26 16.53 12.43 16.53H7.57C6.74 16.53 6.05 15.88 6 15.05L5.42 5M8.33 8.33V13.33M11.67 8.33V13.33" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </form>
</div>

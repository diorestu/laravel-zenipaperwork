@php
    $descriptionLines = $product->description
        ? array_filter(array_map('trim', preg_split('/\s*(?=-)/', $product->description)))
        : [];
@endphp
<div class="flex items-center gap-3">
    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-sm font-semibold text-gray-700 dark:bg-white/5 dark:text-gray-300">
        {{ str($product->name)->substr(0, 1)->upper() }}
    </span>
    <div class="min-w-0">
        <p class="font-medium text-gray-900 dark:text-white/90">{{ $product->name }}</p>
        @if ($descriptionLines)
            <p class="mt-0.5 max-w-[260px] text-xs leading-5 text-gray-500 dark:text-gray-400">
                @foreach ($descriptionLines as $line)
                    <span class="block truncate">{{ $line }}</span>
                @endforeach
            </p>
        @else
            <p class="mt-0.5 max-w-[260px] truncate text-xs text-gray-500 dark:text-gray-400">Tidak ada deskripsi</p>
        @endif
    </div>
</div>

@props(['name', 'label', 'type' => 'text', 'value' => null])
<label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
    <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
    <span class="block">
        <input name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" placeholder="{{ $attributes->get('placeholder', $label) }}" {{ $attributes->except('placeholder')->merge(['class' => 'w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-gray-500']) }}>
        @error($name)<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
    </span>
</label>

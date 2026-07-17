@props(['name', 'label', 'options' => [], 'value' => null])
<label class="grid gap-2 sm:grid-cols-[30%_1fr] sm:items-start">
    <span class="pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
    <span class="block">
        <select name="{{ $name }}" {{ $attributes->merge(['class' => 'w-full appearance-none rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 bg-[url("data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.5%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E")] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat']) }}>
            {{ $slot }}
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) old($name, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
        @error($name)<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
    </span>
</label>

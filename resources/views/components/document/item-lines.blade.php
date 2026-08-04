@props(['items', 'isQuotation' => false])
<table class="w-full text-left text-sm">
    <thead class="border-b border-gray-200 text-xs uppercase text-gray-500">
        <tr>
            <th class="py-2">Item</th>
            <th class="py-2 text-right w-16">Qty</th>
            <th class="py-2 text-right w-28">Harga</th>
            <th class="py-2 text-right w-32">Total</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        @foreach ($items as $item)
            @php
                $description = $item->description;
                if ($isQuotation) {
                    $parts = array_filter(array_map('trim', preg_split('/\s*(?=-)/', $description)));
                    $descriptionHtml = implode('<br>', array_map('e', $parts));
                } else {
                    $descriptionHtml = e($description);
                }
            @endphp
            <tr>
                <td class="py-3 @if($isQuotation) text-xs @endif">{!! $descriptionHtml !!}</td>
                <td class="py-3 text-right">{{ number_format((float) $item->quantity, 2) }}</td>
                <td class="py-3 text-right"><x-money :amount="$item->unit_price" /></td>
                <td class="py-3 text-right"><x-money :amount="$item->line_total" /></td>
            </tr>
        @endforeach
    </tbody>
</table>

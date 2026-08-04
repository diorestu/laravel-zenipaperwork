@extends('layouts.app')

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('quotations.pdf', $quotation) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Unduh PDF</a>
    <form method="POST" action="{{ route('quotations.status', $quotation) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="approved"><button class="rounded-md border border-gray-300 px-3 py-2 text-sm">Setujui</button></form>
    <form method="POST" action="{{ route('quotations.status', $quotation) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><button class="rounded-md border border-gray-300 px-3 py-2 text-sm">Tolak</button></form>
</div>
<div class="grid gap-6 lg:grid-cols-[1fr_24rem]">
    <x-document.preview :document="$quotation" />
    <x-modal>
        <h2 class="font-semibold">Konversi ke Invoice</h2>
        <form method="POST" action="{{ route('quotations.convert', $quotation) }}" class="mt-4 space-y-3">
            @csrf
            <x-form.input name="number" label="Nomor Invoice" :value="'INV-'.now()->format('Ymd-His')" />
            <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Konversi</button>
        </form>
    </x-modal>
</div>
@endsection

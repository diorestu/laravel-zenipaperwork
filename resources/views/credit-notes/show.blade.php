@extends('layouts.app')

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('credit-notes.pdf', $note) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Unduh PDF</a>
    <a href="{{ route('invoices.show', $note->invoice) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Kembali ke Invoice</a>
</div>
<div class="rounded-lg border border-gray-200 bg-white p-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold">Nota Kredit</h1>
            <p class="text-sm text-gray-500">{{ $note->number }}</p>
        </div>
        <x-status-badge :status="$note->status" />
    </div>
    <dl class="mt-6 grid gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-xs uppercase text-gray-500">Diterbitkan Untuk</dt>
            <dd class="text-sm font-medium">{{ $note->client->name }}</dd>
        </div>
        <div>
            <dt class="text-xs uppercase text-gray-500">Tanggal Terbit</dt>
            <dd class="text-sm font-medium">{{ $note->issue_date->format('d M Y') }}</dd>
        </div>
        <div>
            <dt class="text-xs uppercase text-gray-500">Referensi Invoice</dt>
            <dd class="text-sm font-medium">
                <a href="{{ route('invoices.show', $note->invoice) }}" class="text-brand-700 hover:underline">{{ $note->invoice->number }}</a>
            </dd>
        </div>
        <div>
            <dt class="text-xs uppercase text-gray-500">Jumlah</dt>
            <dd class="text-lg font-bold"><x-money :amount="$note->amount" /></dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-xs uppercase text-gray-500">Alasan</dt>
            <dd class="text-sm">{{ $note->reason }}</dd>
        </div>
        @if($note->notes)
            <div class="sm:col-span-2">
                <dt class="text-xs uppercase text-gray-500">Catatan</dt>
                <dd class="text-sm text-gray-700">{{ $note->notes }}</dd>
            </div>
        @endif
    </dl>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-lg font-semibold">Notifications</h1>
    <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="rounded-md border border-gray-300 px-3 py-2 text-sm">Read All</button></form>
</div>
<section class="rounded-lg border border-gray-200 bg-white">
    <div class="divide-y divide-gray-100">
        @forelse ($notifications as $notification)
            <div class="px-5 py-4 text-sm">
                <p class="font-medium">{{ $notification->title }}</p>
                <p class="text-gray-600">{{ $notification->body }}</p>
            </div>
        @empty
            <x-table.empty>Tidak ada notification.</x-table.empty>
        @endforelse
    </div>
</section>
@endsection

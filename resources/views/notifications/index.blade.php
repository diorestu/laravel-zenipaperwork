@extends('layouts.app')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Notifikasi</h1>
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/5">Tandai Semua Dibaca</button>
    </form>
</div>
<section class="rounded-lg border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse ($notifications as $notification)
            <div class="px-5 py-4 text-sm">
                <p class="font-medium text-gray-900 dark:text-white/90">{{ $notification->title }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $notification->body }}</p>
            </div>
        @empty
            <x-table.empty>Tidak ada notifikasi.</x-table.empty>
        @endforelse
    </div>
</section>
@endsection

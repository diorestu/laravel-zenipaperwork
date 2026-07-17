<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Paperwork' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <x-flash />
    <main class="mx-auto flex min-h-screen max-w-md items-center px-6 py-10">
        <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-theme-xs">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>
</body>
</html>

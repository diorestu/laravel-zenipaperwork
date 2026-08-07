<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KXX3GD2W00"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-KXX3GD2W00');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Paperwork' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

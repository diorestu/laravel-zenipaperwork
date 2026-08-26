<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Paperwork - Blog')</title>
    <meta name="description" content="@yield('meta_description', 'Paperwork Blog - Tips, update, dan artikel edukasi untuk mengelola invoice dan billing bisnis Anda.')">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <link rel="icon" type="image/png" sizes="32x32" href="https://paperwork.biz.id/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="https://paperwork.biz.id/images/logo/sq_white.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif !important; }
    </style>
</head>
<body class="bg-[#FBFBFE] font-medium text-gray-900 antialiased selection:bg-brand-500 selection:text-white dark:bg-gray-950 dark:text-white">
    <header class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-950/80">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="/" class="flex items-center">
                <img src="{{ asset('images/logo/logo-header.png') }}" alt="Paperwork" class="h-8 w-auto">
            </a>
            <nav class="hidden items-center gap-8 text-sm font-bold md:flex text-gray-600 dark:text-gray-300">
                <a href="/#fitur" class="hover:text-brand-600 dark:hover:text-white transition">Fitur</a>
                <a href="/#harga" class="hover:text-brand-600 dark:hover:text-white transition">Harga</a>
                <a href="{{ route('blog.index') }}" class="text-brand-600 dark:text-brand-400 transition">Blog</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-5 text-sm font-bold text-white transition hover:bg-brand-700">
                    Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-gray-950 text-white py-12 dark:border-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-gray-400">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo/logo-footer.png') }}" alt="Paperwork" class="h-7 w-auto">
                <span>© {{ date('Y') }} PT Numa Teknologi Nusantara. All rights reserved.</span>
            </div>
        </div>
    </footer>
</body>
</html>

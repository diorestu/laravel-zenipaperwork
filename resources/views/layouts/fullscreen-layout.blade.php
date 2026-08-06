<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo/sq_white.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo/sq_white.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/sq_white.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#111111">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Paperwork">

    <title>{{ $title ?? 'Paperwork' }} | Paperwork</title>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.17.1/firebase-app.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.17.1/firebase-analytics.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBQ8LYQi_ylwA3jX9z73SIJM0cMrWW98j8",
            authDomain: "paperwork-cc867.firebaseapp.com",
            projectId: "paperwork-cc867",
            storageBucket: "paperwork-cc867.firebasestorage.app",
            messagingSenderId: "949071865390",
            appId: "1:949071865390:web:08201f4acff0e05e4b21bf",
            measurementId: "G-HG4PWF6P1K"
        };

        const app = initializeApp(firebaseConfig);
        getAnalytics(app);
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
	                updateTheme() {
	                    const html = document.documentElement;
	                    const body = document.body;
	                    if (this.theme === 'dark') {
	                        html.classList.add('dark');
	                        body?.classList.add('dark', 'bg-gray-900');
	                    } else {
	                        html.classList.remove('dark');
	                        body?.classList.remove('dark', 'bg-gray-900');
	                    }
	                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
	            const theme = savedTheme || systemTheme;
	            if (theme === 'dark') {
	                document.documentElement.classList.add('dark');
	            } else {
	                document.documentElement.classList.remove('dark');
	            }
	        })();
    </script>
</head>

<body x-data="{ 'loaded': true}" x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
const checkMobile = () => {
    if (window.innerWidth < 1280) {
        $store.sidebar.setMobileOpen(false);
        $store.sidebar.isExpanded = false;
    } else {
        $store.sidebar.isMobileOpen = false;
        $store.sidebar.isExpanded = true;
    }
};
window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    <x-flash />
    @yield('content')

</body>

@stack('scripts')

</html>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H0Z5FQGY0E"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-H0Z5FQGY0E');
    </script>

    <!-- Primary Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paperwork - Solusi Invoice, Penawaran & Billing Bisnis Otomatis</title>
    <meta name="title" content="Paperwork - Solusi Invoice, Penawaran & Billing Bisnis Otomatis">
    <meta name="description" content="Paperwork membantu bisnis Anda membuat invoice profesional, dokumen penawaran harga, mengelola klien, dan mengotomatisasi sistem pembayaran tagihan dengan mudah dan rapi.">
    <meta name="keywords" content="aplikasi invoice, buat invoice online, aplikasi penawaran harga, software billing indonesia, kelola tagihan bisnis, aplikasi kasir & billing, paperwork">
    <meta name="author" content="PT Numa Teknologi Nusantara">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon & Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo/sq_white.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo/sq_white.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/sq_white.png') }}">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Paperwork">
    <meta property="og:title" content="Paperwork - Solusi Invoice, Penawaran & Billing Bisnis Otomatis">
    <meta property="og:description" content="Paperwork membantu bisnis Anda membuat invoice profesional, penawaran harga resmi, serta kelola tagihan secara otomatis. Coba Gratis 30 Hari!">
    <meta property="og:image" content="{{ asset('images/logo/logo-header.png') }}">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Paperwork - Solusi Invoice, Penawaran & Billing Bisnis Otomatis">
    <meta property="twitter:description" content="Paperwork membantu bisnis Anda membuat invoice profesional, penawaran harga resmi, serta kelola tagihan secara otomatis. Coba Gratis 30 Hari!">
    <meta property="twitter:image" content="{{ asset('images/logo/logo-header.png') }}">

    <!-- Structured Data (JSON-LD Schema.org) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "SoftwareApplication",
      "name": "Paperwork",
      "operatingSystem": "All",
      "applicationCategory": "BusinessApplication",
      "offers": {
        "@@type": "AggregateOffer",
        "priceCurrency": "IDR",
        "lowPrice": "0",
        "highPrice": "299000",
        "offerCount": "3"
      },
      "description": "Platform Manajemen Invoice, Penawaran Harga, Klien & Billing Perusahaan Otomatis di Indonesia.",
      "publisher": {
        "@@type": "Organization",
        "name": "PT Numa Teknologi Nusantara",
        "logo": "{{ asset('images/logo/logo-header.png') }}"
      }
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif !important; }
    </style>
</head>
<body class="bg-[#FBFBFE] font-medium text-gray-900 antialiased selection:bg-brand-500 selection:text-white dark:bg-gray-950 dark:text-white">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 backdrop-blur-md dark:border-gray-800/80 dark:bg-gray-950/80">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
            <!-- Header Logo (Logo 1.png) -->
            <a href="/" class="flex items-center">
                <img src="{{ asset('images/logo/logo-header.png') }}" alt="Paperwork" class="h-8 w-auto">
            </a>

            <nav class="hidden items-center gap-8 text-sm font-bold md:flex text-gray-600 dark:text-gray-300">
                <a href="#fitur" class="hover:text-brand-600 dark:hover:text-white transition">Fitur Utama</a>
                <a href="#keunggulan" class="hover:text-brand-600 dark:hover:text-white transition">Keunggulan</a>
                <a href="#harga" class="hover:text-brand-600 dark:hover:text-white transition">Harga Paket</a>
                <a href="#faq" class="hover:text-brand-600 dark:hover:text-white transition">FAQ</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-theme-xs transition hover:bg-brand-700 active:scale-[0.98]">
                        <span>Buka Dashboard</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-bold text-gray-700 hover:text-brand-600 sm:block dark:text-gray-300 dark:hover:text-white">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-bold text-white shadow-theme-xs transition hover:bg-brand-700 active:scale-[0.98]">
                        Daftar Gratis
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-32">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.brand.100),white)] opacity-60 dark:bg-[radial-gradient(45rem_50rem_at_top,theme(colors.brand.950),theme(colors.gray.950))] dark:opacity-40"></div>
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="mx-auto max-w-4xl space-y-6">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white leading-[1.15]">
                    Kelola Invoice & Penawaran Bisnis Anda 
                    <span x-data="{
                        words: ['Otomatis & Tanpa Ribet', 'Cepat & Serba Praktis', 'Profesional & Terorganisir'],
                        currentWordIndex: 0,
                        currentCharIndex: 0,
                        isDeleting: false,
                        text: '',
                        type() {
                            let currentFullWord = this.words[this.currentWordIndex];
                            if (this.isDeleting) {
                                this.text = currentFullWord.substring(0, this.currentCharIndex - 1);
                                this.currentCharIndex--;
                            } else {
                                this.text = currentFullWord.substring(0, this.currentCharIndex + 1);
                                this.currentCharIndex++;
                            }

                            let typeSpeed = this.isDeleting ? 40 : 80;

                            if (!this.isDeleting && this.currentCharIndex === currentFullWord.length) {
                                typeSpeed = 2200;
                                this.isDeleting = true;
                            } else if (this.isDeleting && this.currentCharIndex === 0) {
                                this.isDeleting = false;
                                this.currentWordIndex = (this.currentWordIndex + 1) % this.words.length;
                                typeSpeed = 400;
                            }

                            setTimeout(() => this.type(), typeSpeed);
                        }
                    }" x-init="type()" class="text-brand-600 dark:text-brand-400 inline-block min-h-[1.2em]">
                        <span x-text="text"></span><span class="animate-pulse text-brand-600 dark:text-brand-400">|</span>
                    </span>
                </h1>

                <p class="mx-auto max-w-2xl text-base font-medium text-gray-600 sm:text-lg dark:text-gray-300 leading-relaxed">
                    Tinggalkan pembuatan dokumen manual. Terbitkan invoice profesional, penawaran resmi, terima pembayaran QRIS instan, dan kelola arus kas bisnis Anda secara real-time.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-brand-600 px-8 text-base font-bold text-white shadow-theme-md transition hover:bg-brand-700 active:scale-[0.98]">
                            <span>Buka Dashboard Perusahaan</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/628811841064?text=Halo%20Paperwork,%20saya%20ingin%20bertanya%20mengenai%20layanan%20Paperwork." target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex h-12 items-center justify-center rounded-xl border border-gray-300 bg-white px-8 text-base font-bold text-gray-800 shadow-sm transition hover:bg-gray-50 hover:border-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                            <span>Hubungi Kami</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-brand-600 px-8 text-base font-bold text-white shadow-theme-md transition hover:bg-brand-700 active:scale-[0.98]">
                            <span>Coba Uji Coba Gratis 30 Hari</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/628811841064?text=Halo%20Paperwork,%20saya%20ingin%20bertanya%20mengenai%20layanan%20Paperwork." target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex h-12 items-center justify-center rounded-xl border border-gray-300 bg-white px-8 text-base font-bold text-gray-800 shadow-sm transition hover:bg-gray-50 hover:border-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                            <span>Hubungi Kami</span>
                        </a>
                    @endauth
                </div>

                <div class="flex items-center justify-center gap-6 pt-4 text-xs font-bold text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tanpa Kartu Kredit</span>
                    <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Gratis Selamanya (Free Tier)</span>
                    <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Setup 1 Menit</span>
                </div>
            </div>

            <!-- Hero 4 Floating Stats Counter Items -->
            <div x-data="{
                clientCount: 0,
                invoiceCount: 0,
                revenueCount: 0,
                satisfactionCount: 0,
                animated: false,
                init() {
                    let observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !this.animated) {
                                this.animated = true;
                                this.animateValues();
                            }
                        });
                    }, { threshold: 0.2 });
                    observer.observe(this.$el);
                },
                animateValues() {
                    let duration = 2000;
                    let startTime = null;
                    let step = (timestamp) => {
                        if (!startTime) startTime = timestamp;
                        let progress = Math.min((timestamp - startTime) / duration, 1);
                        let easeProgress = 1 - Math.pow(1 - progress, 3);
                        this.clientCount = Math.floor(easeProgress * 500);
                        this.invoiceCount = Math.floor(easeProgress * 12500);
                        this.revenueCount = (easeProgress * 2.8).toFixed(1);
                        this.satisfactionCount = (easeProgress * 99.8).toFixed(1);
                        if (progress < 1) {
                            window.requestAnimationFrame(step);
                        }
                    };
                    window.requestAnimationFrame(step);
                }
            }" class="mt-14 relative mx-auto max-w-5xl">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                    <!-- Item 1: Total Klien -->
                    <div class="flex flex-col items-center justify-center text-center p-6 rounded-2xl bg-white/80 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-brand-500/40">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Klien Aktif</span>
                        <div class="text-3xl sm:text-4xl font-extrabold text-brand-600 dark:text-brand-400 mt-2">
                            <span x-text="clientCount">0</span>+
                        </div>
                    </div>

                    <!-- Item 2: Invoice Diterbitkan -->
                    <div class="flex flex-col items-center justify-center text-center p-6 rounded-2xl bg-white/80 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-indigo-500/40">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice Diterbitkan</span>
                        <div class="text-3xl sm:text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2">
                            <span x-text="invoiceCount.toLocaleString('id-ID')">0</span>+
                        </div>
                    </div>

                    <!-- Item 3: Transaksi Diproses -->
                    <div class="flex flex-col items-center justify-center text-center p-6 rounded-2xl bg-white/80 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-emerald-500/40">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nilai Transaksi</span>
                        <div class="text-3xl sm:text-4xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">
                            Rp <span x-text="revenueCount">0</span>M+
                        </div>
                    </div>

                    <!-- Item 4: Kepuasan Pengguna -->
                    <div class="flex flex-col items-center justify-center text-center p-6 rounded-2xl bg-white/80 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-violet-500/40">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kepuasan Pengguna</span>
                        <div class="text-3xl sm:text-4xl font-extrabold text-violet-600 dark:text-violet-400 mt-2">
                            <span x-text="satisfactionCount">0</span>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="fitur" class="py-20 bg-white dark:bg-gray-900 border-y border-gray-200/60 dark:border-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Fitur Unggulan</span>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">Segala Yang Anda Butuhkan Untuk Mengelola Billing</h2>
                <p class="text-base font-medium text-gray-600 dark:text-gray-300">Paperwork dirancang khusus agar Anda dapat bekerja lebih cepat, rapi, dan profesional dalam menerbitkan tagihan bisnis.</p>
            </div>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- 1. Invoice & Penawaran Instan -->
                <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 transition hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-950/50">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400 mb-4">
                        <x-heroicon-o-document-text class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Invoice & Penawaran Instan</h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">Buat dokumen invoice dan quotation resmi dalam hitungan detik. Ekspor ke PDF berkualitas tinggi dengan watermark otomatis.</p>
                </div>

                <!-- 2. Statistik & Laporan Real-Time -->
                <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 transition hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-950/50">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 mb-4">
                        <x-heroicon-o-chart-bar class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Statistik & Laporan Real-Time</h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">Pantau ringkasan pendapatan diterbitkan vs diterima, piutang aktif, serta analisis arus kas bisnis Anda secara otomatis.</p>
                </div>

                <!-- 3. Kustom Penomoran Template -->
                <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 transition hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-950/50">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 mb-4">
                        <x-heroicon-o-adjustments-horizontal class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kustom penomoran Template</h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">Sesuaikan format penomoran invoice dengan tag dinamis seperti <code class="text-brand-600 font-bold">{PREFIX}</code>, <code class="text-brand-600 font-bold">{YYYY}</code>, <code class="text-brand-600 font-bold">{ROMAN}</code>, dan <code class="text-brand-600 font-bold">{NUMBER}</code>.</p>
                </div>

                <!-- 4. Manajemen Klien & Produk -->
                <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 transition hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-950/50">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 mb-4">
                        <x-heroicon-o-user-group class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Manajemen Klien & Produk</h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">Simpan data klien dan katalog produk secara terorganisir. Sistem memantau limit kuota secara cerdas sesuai paket Anda.</p>
                </div>

                <!-- 5. PWA Mobile Workspace -->
                <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 transition hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-950/50">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400 mb-4">
                        <x-heroicon-o-device-phone-mobile class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">PWA Mobile Workspace</h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">Install Paperwork sebagai aplikasi mobile di HP Android/iOS Anda tanpa perlu mengunduh di Play Store/App Store.</p>
                </div>

                <!-- 6. Kalender & Jatuh Tempo -->
                <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 transition hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-950/50">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 mb-4">
                        <x-heroicon-o-calendar-days class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kalender & Jatuh Tempo</h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">Pantau tanggal jatuh tempo tagihan di tampilan kalender interaktif untuk mencegah keterlambatan pembayaran.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="harga" class="py-20" x-data="{ billingPeriod: 'monthly' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Pilihan Paket</span>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">Harga Transparan Tanpa Biaya Tersembunyi</h2>
                <p class="text-base font-medium text-gray-600 dark:text-gray-300">Pilih paket langganan yang sesuai dengan skala dan pertumbuhan bisnis Anda.</p>

                <!-- Toggle Period Bulanan vs Tahunan -->
                <div class="pt-4 flex justify-center">
                    <div class="inline-flex rounded-xl border border-gray-200 bg-white p-1 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <button type="button" @click="billingPeriod = 'monthly'" :class="billingPeriod === 'monthly' ? 'bg-brand-600 text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'" class="rounded-lg px-5 py-2 text-xs font-bold transition">
                            Bulanan
                        </button>
                        <button type="button" @click="billingPeriod = 'yearly'" :class="billingPeriod === 'yearly' ? 'bg-brand-600 text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'" class="rounded-lg px-5 py-2 text-xs font-bold transition flex items-center gap-1.5">
                            <span>Tahunan</span>
                            <span class="rounded-full bg-emerald-500 px-2 py-0.5 text-[10px] font-extrabold text-white">Diskon 10%</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Free Tier -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between dark:border-gray-800 dark:bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Paket Gratis</span>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Free Tier</h3>
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">Rp 0</span>
                            <span class="text-xs font-medium text-gray-500">/ selamanya</span>
                        </div>
                        <ul class="mt-6 space-y-2.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Kelola hingga 20 klien</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Kelola hingga 20 produk/layanan</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Buat 25 dokumen/bulan</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Watermark PAPERWORK FREE</li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="mt-8 block w-full rounded-xl border border-gray-300 bg-white py-2.5 text-center text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">Mulai Gratis</a>
                </div>

                <!-- Starter Plan -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between dark:border-gray-800 dark:bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-brand-600 dark:text-brand-400 uppercase">Starter</span>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Starter</h3>
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white" x-show="billingPeriod === 'monthly'">Rp 25.000</span>
                            <span class="text-3xl font-bold text-gray-900 dark:text-white" x-show="billingPeriod === 'yearly'" style="display: none;">Rp 270.000</span>
                            <span class="text-xs font-medium text-gray-500" x-text="billingPeriod === 'monthly' ? '/ bulan' : '/ tahun'">/ bulan</span>
                        </div>
                        <ul class="mt-6 space-y-2.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Kelola hingga 100 klien</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Kelola hingga 100 produk atau layanan</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Buat hingga 50 invoice/quotation per bulan</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Unduh PDF invoice dan penawaran</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Riwayat pembayaran manual</li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="mt-8 block w-full rounded-xl bg-brand-600 py-2.5 text-center text-xs font-bold text-white hover:bg-brand-700">Pilih Starter</a>
                </div>

                <!-- Business Plan (Featured) -->
                <div class="relative rounded-2xl border-2 border-brand-500 bg-white p-6 shadow-xl flex flex-col justify-between dark:bg-gray-900">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-brand-500 px-3 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">Paling Populer 🔥</span>
                    <div>
                        <span class="text-xs font-bold text-brand-600 dark:text-brand-400 uppercase">Business</span>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Business</h3>
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-brand-600 dark:text-brand-400" x-show="billingPeriod === 'monthly'">Rp 99.000</span>
                            <span class="text-3xl font-bold text-brand-600 dark:text-brand-400" x-show="billingPeriod === 'yearly'" style="display: none;">Rp 1.069.200</span>
                            <span class="text-xs font-medium text-gray-500" x-text="billingPeriod === 'monthly' ? '/ bulan' : '/ tahun'">/ bulan</span>
                        </div>
                        <ul class="mt-6 space-y-2.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Kelola hingga 500 klien</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Kelola hingga 500 produk atau layanan</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Buat penawaran tanpa batas</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Buat hingga 500 invoice</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Pembayaran bertahap & catatan termin</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Integrasi Pembayaran QRIS Otomatis</li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="mt-8 block w-full rounded-xl bg-brand-600 py-2.5 text-center text-xs font-bold text-white hover:bg-brand-700 shadow-md">Pilih Business</a>
                </div>

                <!-- Enterprise Plan -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between dark:border-gray-800 dark:bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase">Enterprise</span>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Enterprise</h3>
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white" x-show="billingPeriod === 'monthly'">Rp 299.000</span>
                            <span class="text-3xl font-bold text-gray-900 dark:text-white" x-show="billingPeriod === 'yearly'" style="display: none;">Rp 3.229.200</span>
                            <span class="text-xs font-medium text-gray-500" x-text="billingPeriod === 'monthly' ? '/ bulan' : '/ tahun'">/ bulan</span>
                        </div>
                        <ul class="mt-6 space-y-2.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Klien, produk, penawaran & invoice tanpa batas</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Semua fitur Business</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Prioritas dukungan operasional</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Pendampingan setup dokumen perusahaan</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Review konfigurasi billing khusus</li>
                        </ul>
                    </div>
                    <a href="https://wa.me/628811841064?text=Halo%20Paperwork,%20saya%20tertarik%20dengan%20Paket%20Enterprise" target="_blank" rel="noopener noreferrer" class="mt-8 flex items-center justify-center gap-1.5 w-full rounded-xl border border-gray-300 bg-white py-2.5 text-center text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition">Hubungi Kami via WhatsApp</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-white dark:bg-gray-900 border-t border-gray-200/60 dark:border-gray-800">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Pertanyaan Umum</span>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">Pertanyaan Sering Diajukan (FAQ)</h2>
            </div>

            <div class="space-y-4" x-data="{ open: null }">
                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-950/50">
                    <button @click="open = open === 1 ? null : 1" class="flex w-full items-center justify-between text-left text-sm font-bold text-gray-900 dark:text-white">
                        <span>Apakah Paket Gratis (Free Tier) berlaku selamanya?</span>
                        <span class="text-lg font-bold" x-text="open === 1 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="open === 1" class="mt-3 text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed" style="display: none;">
                        Ya, Paket Gratis berlaku selamanya tanpa batas waktu uji coba! Anda dapat mengelola hingga 20 klien, 20 produk, dan 25 dokumen per bulan tanpa biaya sama sekali.
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-950/50">
                    <button @click="open = open === 2 ? null : 2" class="flex w-full items-center justify-between text-left text-sm font-bold text-gray-900 dark:text-white">
                        <span>Bagaimana cara kerja verifikasi pembayaran QRIS?</span>
                        <span class="text-lg font-bold" x-text="open === 2 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="open === 2" class="mt-3 text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed" style="display: none;">
                        Saat pelanggan memindai QRIS dan menyelesaikan pembayaran dari aplikasi e-wallet / mobile banking, sistem pembayaran akan mengirimkan sinyal Webhook otomatis ke sistem kami untuk mengonfirmasi transaksi secara instan tanpa perlu cek mutasi manual.
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-950/50">
                    <button @click="open = open === 3 ? null : 3" class="flex w-full items-center justify-between text-left text-sm font-bold text-gray-900 dark:text-white">
                        <span>Apakah saya bisa mengatur format nomor invoice sesuai keinginan perusahaan?</span>
                        <span class="text-lg font-bold" x-text="open === 3 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="open === 3" class="mt-3 text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed" style="display: none;">
                        Bisa sekali! Anda dapat menentukan Prefix, format tanggal/tahun/bulan romawi, serta jumlah digit nomor urut di menu Pengaturan Perusahaan.
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-950/50">
                    <button @click="open = open === 4 ? null : 4" class="flex w-full items-center justify-between text-left text-sm font-bold text-gray-900 dark:text-white">
                        <span>Bagaimana cara mengunduh dan mencetak dokumen Invoice / Penawaran?</span>
                        <span class="text-lg font-bold" x-text="open === 4 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="open === 4" class="mt-3 text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed" style="display: none;">
                        Setiap dokumen invoice dan penawaran dapat langsung diunduh dalam format PDF berkualitas tinggi atau dicetak secara instan hanya dengan 1 kali klik dari dashboard Anda.
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-950/50">
                    <button @click="open = open === 5 ? null : 5" class="flex w-full items-center justify-between text-left text-sm font-bold text-gray-900 dark:text-white">
                        <span>Apakah Paperwork dapat diakses dari perangkat HP / Smartphone?</span>
                        <span class="text-lg font-bold" x-text="open === 5 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="open === 5" class="mt-3 text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed" style="display: none;">
                        Ya! Paperwork telah mendukung teknologi Progressive Web App (PWA), sehingga Anda dapat meng-install dan mengakses aplikasi secara langsung melalui HP Android maupun iOS tanpa perlu mengunduh dari toko aplikasi.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-gray-950 text-white py-12 dark:border-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-gray-400">
            <!-- Footer Logo (Logo 2.png) -->
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('images/logo/logo-footer.png') }}" alt="Paperwork" class="h-7 w-auto">
                </a>
                <span class="font-medium text-gray-400">© {{ date('Y') }} PT Numa Teknologi Nusantara. All rights reserved.</span>
            </div>

            <!-- Social Media Icons (Instagram, Facebook, LinkedIn) -->
            <div class="flex items-center gap-3 text-gray-400">
                <!-- Instagram -->
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="rounded-lg p-2 hover:bg-gray-800 hover:text-white transition-colors" title="Instagram">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>

                <!-- Facebook -->
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="rounded-lg p-2 hover:bg-gray-800 hover:text-white transition-colors" title="Facebook">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>

                <!-- LinkedIn -->
                <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="rounded-lg p-2 hover:bg-gray-800 hover:text-white transition-colors" title="LinkedIn">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                </a>
            </div>

            <div class="flex items-center gap-6 font-medium">
                <a href="{{ route('privacy-policy') }}" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="{{ route('terms-of-service') }}" class="hover:text-white transition">Syarat & Ketentuan</a>
                <a href="{{ route('pwa.install') }}" class="hover:text-white transition">Install Mobile App</a>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/628811841064?text=Halo%20Paperwork,%20saya%20ingin%20bertanya%20mengenai%20layanan%20Paperwork." 
       target="_blank" 
       rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 flex items-center justify-center rounded-full bg-[#25D366] px-4 py-3 text-white shadow-2xl shadow-emerald-500/40 animate-bounce transition-all duration-300 hover:animate-none hover:scale-110 hover:bg-[#20ba5a] hover:shadow-emerald-500/60 group"
       title="Hubungi Kami via WhatsApp (+628811841064)"
    >
        <img src="{{ asset('images/whatsapp-badge.svg') }}" alt="WhatsApp" class="h-6 w-auto object-contain drop-shadow-md">
    </a>
</body>
</html>

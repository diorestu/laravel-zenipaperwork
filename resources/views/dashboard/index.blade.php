@extends('layouts.app')

@section('content')
<div class="space-y-6"
     x-data="{
        showOnboarding: false,
        step: 1,
        maxSteps: 4,
        storageKey: 'paperwork_onboarding_user_' + @js(auth()->id()),
        init() {
            if (!localStorage.getItem(this.storageKey)) {
                setTimeout(() => { this.showOnboarding = true; }, 500);
            }
        },
        closeOnboarding() {
            this.showOnboarding = false;
            localStorage.setItem(this.storageKey, 'completed');
        },
        openOnboarding() {
            this.step = 1;
            this.showOnboarding = true;
        }
     }"
>
    <!-- Header Dashboard dengan Tombol Ulangi Onboarding -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola ringkasan kinerja, invoice, penawaran, dan pembayaran perusahaan {{ auth()->user()->company?->name }}.</p>
        </div>
        <div>
            <button @click="openOnboarding()" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 hover:border-brand-300 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                <span>💡 Panduan Onboarding</span>
            </button>
        </div>
    </div>

    <!-- Modal Onboarding User Baru -->
    <div x-show="showOnboarding" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm"
         style="display: none;"
    >
        <div @click.away="closeOnboarding()" 
             class="relative w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900 dark:border dark:border-gray-800"
        >
            <!-- Top Gradient Header -->
            <div class="bg-gradient-to-r from-brand-600 to-indigo-600 px-6 py-6 text-white relative">
                <button @click="closeOnboarding()" type="button" class="absolute top-4 right-4 rounded-full bg-white/10 p-1.5 text-white/80 hover:bg-white/20 hover:text-white transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-200 mb-1">
                    <span>Langkah <span x-text="step"></span> dari <span x-text="maxSteps"></span></span>
                </div>
                <h2 class="text-xl font-extrabold" x-show="step === 1">Selamat Datang di Paperwork! 🚀</h2>
                <h2 class="text-xl font-extrabold" x-show="step === 2" style="display: none;">Penomoran Invoice Kustom ⚙️</h2>
                <h2 class="text-xl font-extrabold" x-show="step === 3" style="display: none;">Pembayaran QRIS & Notifikasi 💳</h2>
                <h2 class="text-xl font-extrabold" x-show="step === 4" style="display: none;">Panduan Mulai Cepat 🏁</h2>
                <p class="text-xs text-white/80 mt-1" x-show="step === 1">Platform manajemen invoice, penawaran, dan pembayaran profesional untuk bisnis Anda.</p>
                <p class="text-xs text-white/80 mt-1" x-show="step === 2" style="display: none;">Atur template nomor invoice otomatis sesuai identitas standar perusahaan Anda.</p>
                <p class="text-xs text-white/80 mt-1" x-show="step === 3" style="display: none;">Terima pembayaran QRIS Pakasir instan dengan verifikasi otomatis tanpa perlu cek manual.</p>
                <p class="text-xs text-white/80 mt-1" x-show="step === 4" style="display: none;">Empat langkah mudah untuk mulai menerbitkan invoice pertama Anda.</p>
            </div>

            <!-- Slide Content Container -->
            <div class="p-6">
                <!-- Slide 1: Welcome -->
                <div x-show="step === 1" class="space-y-4">
                    <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-850/50 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">
                                📄
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Kelola Invoice & Penawaran</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Buat dokumen invoice dan quotation profesional dengan ekspor PDF siap cetak.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 border-t border-gray-200/60 dark:border-gray-800 pt-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                                📊
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pantau Kinerja & Piutang Real-Time</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Grafik analisis pendapatan, pelacakan invoice jatuh tempo, dan statistik transaksi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Numbering Template -->
                <div x-show="step === 2" style="display: none;" class="space-y-4">
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4 dark:border-indigo-500/20 dark:bg-indigo-500/10 space-y-2">
                        <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">Format Template Nomor Invoice:</span>
                        <p class="font-mono text-sm font-bold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-900 px-3 py-2 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            INV/2026/08/0001
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Gunakan tag dinamis seperti <code class="text-brand-600 font-bold">{PREFIX}</code>, <code class="text-brand-600 font-bold">{YYYY}</code>, <code class="text-brand-600 font-bold">{MM}</code>, <code class="text-brand-600 font-bold">{ROMAN}</code>, dan <code class="text-brand-600 font-bold">{NUMBER}</code> untuk menyesuaikan skema penomoran invoice perusahaan Anda di menu Pengaturan Perusahaan.</p>
                    </div>
                </div>

                <!-- Slide 3: QRIS & Payments -->
                <div x-show="step === 3" style="display: none;" class="space-y-4">
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10 space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📱</span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">QRIS Pakasir Automated Webhook</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">Pelanggan bayar via QRIS ➔ Webhook verifikasi otomatis ➔ Status transaksi langsung Lunas.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 border-t border-emerald-200/60 dark:border-emerald-800/60 pt-3">
                            <span class="text-2xl">📱</span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Akses PWA Mobile App</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">Gunakan Paperwork langsung dari HP Android / iPhone Anda kapan saja.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4: Quick Start Checklist -->
                <div x-show="step === 4" style="display: none;" class="space-y-3">
                    <div class="space-y-2">
                        <a href="{{ route('settings.company') }}" class="flex items-center justify-between rounded-xl border border-gray-200 p-3 hover:border-brand-500 hover:bg-brand-50/30 transition dark:border-gray-800 dark:hover:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">1</span>
                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Lengkapi Profil & Format Invoice</span>
                            </div>
                            <span class="text-xs font-bold text-brand-600 dark:text-brand-400">Buka →</span>
                        </a>

                        <a href="{{ route('settings.bank-accounts') }}" class="flex items-center justify-between rounded-xl border border-gray-200 p-3 hover:border-brand-500 hover:bg-brand-50/30 transition dark:border-gray-800 dark:hover:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">2</span>
                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Atur Rekening Pembayaran Bank</span>
                            </div>
                            <span class="text-xs font-bold text-brand-600 dark:text-brand-400">Buka →</span>
                        </a>

                        <a href="{{ route('clients.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 p-3 hover:border-brand-500 hover:bg-brand-50/30 transition dark:border-gray-800 dark:hover:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">3</span>
                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Tambah Data Klien Pertama</span>
                            </div>
                            <span class="text-xs font-bold text-brand-600 dark:text-brand-400">Buka →</span>
                        </a>

                        <a href="{{ route('invoices.index', ['modal' => 'create']) }}" class="flex items-center justify-between rounded-xl border border-brand-200 bg-brand-50/50 p-3 hover:border-brand-500 transition dark:border-brand-500/20 dark:bg-brand-500/10">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">4</span>
                                <span class="text-xs font-bold text-brand-700 dark:text-brand-300">Buat Invoice Pertama Anda</span>
                            </div>
                            <span class="text-xs font-bold text-brand-600 dark:text-brand-400">Buat Sekarang →</span>
                        </a>
                    </div>
                </div>

                <!-- Footer Navigation Controls -->
                <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
                    <!-- Dots Pagination -->
                    <div class="flex items-center gap-1.5">
                        <template x-for="i in maxSteps" :key="i">
                            <button type="button" @click="step = i" 
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="step === i ? 'w-6 bg-brand-500' : 'w-2 bg-gray-300 dark:bg-gray-700'"
                            ></button>
                        </template>
                    </div>

                    <div class="flex items-center gap-2">
                        <button x-show="step > 1" @click="step--" type="button" class="rounded-xl border border-gray-200 px-3.5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                            Kembali
                        </button>
                        <button x-show="step < maxSteps" @click="step++" type="button" class="rounded-xl bg-brand-500 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-600 transition shadow-sm">
                            Lanjut →
                        </button>
                        <button x-show="step === maxSteps" @click="closeOnboarding()" type="button" class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-600 transition shadow-sm">
                            Mulai Gunakan Paperwork 🚀
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="dashboard-stats-grid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach ($stats as $stat)
            @php
                $statTheme = [
                    'border-sky-100 bg-sky-50/80 text-sky-700 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-300',
                    'border-violet-100 bg-violet-50/80 text-violet-700 dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-300',
                    'border-emerald-100 bg-emerald-50/80 text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                    'border-amber-100 bg-amber-50/80 text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300',
                    'border-rose-100 bg-rose-50/80 text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300',
                ][$loop->index % 5];
            @endphp
            <a href="{{ $stat['href'] }}" class="group rounded-lg border p-4 shadow-theme-xs transition hover:-translate-y-0.5 hover:shadow-theme-md {{ $statTheme }}">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-current/75">{{ $stat['label'] }}</p>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/70 text-current shadow-theme-xs transition group-hover:bg-white dark:bg-white/10 dark:group-hover:bg-white/20">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 17L17 7M17 7H9M17 7V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-xl font-semibold text-gray-950 dark:text-white/90">{{ $stat['value'] }}</p>
                <p class="mt-1 text-xs text-current/65">{{ $stat['meta'] }}</p>
            </a>
        @endforeach
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">Tren Invoice</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nilai diterbitkan vs pembayaran diterima</p>
                </div>
                <span class="rounded-full border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600 dark:border-gray-700 dark:text-gray-400">6 bulan</span>
            </div>
            <div id="invoiceRevenueLineChart" class="mt-4 h-[320px]">
                <canvas id="invoiceRevenueLineCanvas"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">Status Invoice</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Distribusi dokumen saat ini</p>
                </div>
                <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">Lihat invoice</a>
            </div>
            <div id="invoiceStatusBarChart" class="mt-4 h-[320px]">
                <canvas id="invoiceStatusBarCanvas"></canvas>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">Aktivitas Terbaru</h2>
        <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($recentInvoices->take(5) as $invoice)
                <a href="{{ route('invoices.show', $invoice) }}" class="flex justify-between py-3 text-sm text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-brand-400 transition-colors">
                    <span class="font-medium">{{ $invoice->number }} - {{ $invoice->client->name }}</span>
                    <x-status-badge :status="$invoice->status" />
                </a>
            @empty
                <x-table.empty>Belum ada invoice.</x-table.empty>
            @endforelse
        </div>
    </section>
</div>

<script>
    window.dashboardChartData = @json($chartData);

    document.addEventListener('DOMContentLoaded', () => {
        const data = window.dashboardChartData;
        const moneyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        let lineChartInstance = null;
        let barChartInstance = null;

        function getChartColors() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                textColor: isDark ? '#9CA3AF' : '#667085',
                legendColor: isDark ? '#D1D5DB' : '#475467',
                gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : '#E5E7EB',
            };
        }

        function renderCharts() {
            const colors = getChartColors();

            // Render Line Chart
            if (window.Chart && document.querySelector('#invoiceRevenueLineCanvas')) {
                if (lineChartInstance) lineChartInstance.destroy();

                lineChartInstance = new Chart(document.querySelector('#invoiceRevenueLineCanvas'), {
                    type: 'line',
                    data: {
                        labels: data.months,
                        datasets: [
                            {
                                label: 'Diterbitkan',
                                data: data.issued,
                                borderColor: '#465FFF',
                                backgroundColor: 'rgba(70, 95, 255, 0.12)',
                                borderWidth: 3,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#465FFF',
                                tension: 0.35,
                                fill: true,
                            },
                            {
                                label: 'Diterima',
                                data: data.collected,
                                borderColor: '#12B76A',
                                backgroundColor: 'rgba(18, 183, 106, 0.12)',
                                borderWidth: 3,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#12B76A',
                                tension: 0.35,
                                fill: true,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                align: 'start',
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    color: colors.legendColor,
                                    usePointStyle: true,
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${moneyFormatter.format(context.parsed.y)}`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    color: colors.textColor,
                                },
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: colors.gridColor,
                                    borderDash: [4, 4],
                                },
                                ticks: {
                                    color: colors.textColor,
                                    callback: (value) => moneyFormatter.format(value),
                                },
                            },
                        },
                    },
                });
            }

            // Render Bar Chart
            if (window.Chart && document.querySelector('#invoiceStatusBarCanvas')) {
                if (barChartInstance) barChartInstance.destroy();

                barChartInstance = new Chart(document.querySelector('#invoiceStatusBarCanvas'), {
                    type: 'bar',
                    data: {
                        labels: data.statusLabels,
                        datasets: [
                            {
                                label: 'Invoice',
                                data: data.statusCounts,
                                backgroundColor: '#465FFF',
                                borderColor: '#465FFF',
                                borderRadius: 6,
                                maxBarThickness: 42,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        onClick: (_event, elements) => {
                            const item = elements[0];
                            const status = item ? data.statusKeys[item.index] : null;

                            if (status) {
                                window.location.href = `${@json(route('invoices.index'))}?status=${status}`;
                            }
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.parsed.y} invoice`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    color: colors.textColor,
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textColor,
                                    precision: 0,
                                },
                                grid: {
                                    color: colors.gridColor,
                                    borderDash: [4, 4],
                                },
                            },
                        },
                    },
                });
            }
        }

        renderCharts();

        // Re-render charts when dark mode is toggled via MutationObserver
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    renderCharts();
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    });
</script>
@endsection

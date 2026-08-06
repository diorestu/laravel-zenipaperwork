@extends('layouts.app')

@section('content')
<div class="space-y-6">
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
            @forelse ($recentInvoices as $invoice)
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

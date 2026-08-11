@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Bar -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laporan Keuangan & Rekap Pajak</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Analisis arus kas, laba rugi, pemetaan umur piutang, serta rekapitulasi pajak perusahaan.</p>
        </div>

        <!-- Export Buttons & Date Filter -->
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div class="flex items-center gap-1 bg-white p-1 rounded-lg border border-gray-200 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded px-2 py-1 text-xs border-0 focus:ring-0 dark:bg-transparent dark:text-white">
                <span class="text-xs text-gray-400">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded px-2 py-1 text-xs border-0 focus:ring-0 dark:bg-transparent dark:text-white">
                <button type="submit" class="rounded bg-brand-500 px-3 py-1 text-xs font-semibold text-white hover:bg-brand-600">Filter</button>
            </div>

            <div class="flex items-center gap-1">
                <a href="{{ route('reports.export', ['type' => $tab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh CSV / E-Faktur
                </a>
                <a href="{{ route('reports.pdf', ['type' => $tab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-1 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white shadow-theme-xs hover:bg-gray-800 dark:bg-brand-500 dark:hover:bg-brand-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-800">
        <nav class="-mb-px flex space-x-6 overflow-x-auto">
            <a href="{{ route('reports.index', ['tab' => 'cash-flow', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="whitespace-nowrap pb-3 text-xs font-bold transition border-b-2 {{ $tab === 'cash-flow' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
                💵 Arus Kas (Cash Flow)
            </a>
            <a href="{{ route('reports.index', ['tab' => 'profit-loss', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="whitespace-nowrap pb-3 text-xs font-bold transition border-b-2 {{ $tab === 'profit-loss' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
                📊 Laba Rugi (Profit & Loss)
            </a>
            <a href="{{ route('reports.index', ['tab' => 'aging-ar', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="whitespace-nowrap pb-3 text-xs font-bold transition border-b-2 {{ $tab === 'aging-ar' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
                ⏱ Umur Piutang (Aging AR)
            </a>
            <a href="{{ route('reports.index', ['tab' => 'tax-summary', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="whitespace-nowrap pb-3 text-xs font-bold transition border-b-2 {{ $tab === 'tax-summary' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
                🏛 Rekap Pajak & E-Faktur
            </a>
        </nav>
    </div>

    <!-- TAB 1: CASH FLOW -->
    @if ($tab === 'cash-flow')
        <div class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-5 dark:border-emerald-800/40 dark:bg-emerald-950/20">
                    <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">Total Kas Masuk (Inflow)</p>
                    <h3 class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($reportData['total_inflow'], 0, ',', '.') }}</h3>
                </div>

                <div class="rounded-xl border border-red-200 bg-red-50/50 p-5 dark:border-red-800/40 dark:bg-red-950/20">
                    <p class="text-xs font-semibold text-red-800 dark:text-red-300">Total Kas Keluar (Outflow)</p>
                    <h3 class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($reportData['total_outflow'], 0, ',', '.') }}</h3>
                </div>

                <div class="rounded-xl border border-brand-200 bg-brand-50/50 p-5 dark:border-brand-800/40 dark:bg-brand-950/20">
                    <p class="text-xs font-semibold text-brand-800 dark:text-brand-300">Arus Kas Bersih (Net Cashflow)</p>
                    <h3 class="mt-2 text-2xl font-bold text-brand-600 dark:text-brand-400">Rp {{ number_format($reportData['net_cashflow'], 0, ',', '.') }}</h3>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Rincian Pembayaran Masuk Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 uppercase text-gray-700 dark:bg-white/5 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-2.5">Tanggal</th>
                                <th class="px-4 py-2.5">Nomor Invoice</th>
                                <th class="px-4 py-2.5">Klien</th>
                                <th class="px-4 py-2.5">Metode Pembayaran</th>
                                <th class="px-4 py-2.5 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($reportData['payments'] as $pmt)
                                <tr>
                                    <td class="px-4 py-2.5">{{ $pmt->paid_at?->format('d M Y') }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-brand-600">{{ $pmt->invoice?->number }}</td>
                                    <td class="px-4 py-2.5">{{ $pmt->invoice?->client?->name }}</td>
                                    <td class="px-4 py-2.5 uppercase">{{ $pmt->method ?? 'Transfer Bank' }}</td>
                                    <td class="px-4 py-2.5 text-right font-bold text-emerald-600">+Rp {{ number_format((float)$pmt->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi pembayaran dalam periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: PROFIT & LOSS -->
    @if ($tab === 'profit-loss')
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03] space-y-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white border-b pb-3 dark:border-gray-800">Laporan Laba Rugi Periode {{ $reportData['start_date'] }} s/d {{ $reportData['end_date'] }}</h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b dark:border-gray-800">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Pendapatan Kotor (Subtotal Invoice)</span>
                    <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($reportData['gross_revenue'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b dark:border-gray-800 text-amber-600">
                    <span>Total Diskon (-)</span>
                    <span class="font-bold">-Rp {{ number_format($reportData['total_discount'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b font-bold bg-gray-50 dark:bg-white/5 px-3 rounded-lg text-gray-900 dark:text-white">
                    <span>PENDAPATAN BERSIH</span>
                    <span>Rp {{ number_format($reportData['net_revenue'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b dark:border-gray-800 text-red-600">
                    <span>Total Beban & Pengeluaran Operasional (-)</span>
                    <span class="font-bold">-Rp {{ number_format($reportData['total_expenses'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b dark:border-gray-800 text-red-600">
                    <span>Potongan PPh 23 / Pemotongan (-)</span>
                    <span class="font-bold">-Rp {{ number_format($reportData['total_pph'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-3 border-t-2 border-brand-500 font-extrabold text-base bg-brand-50/50 dark:bg-brand-500/10 px-4 rounded-xl text-brand-700 dark:text-brand-300">
                    <span>LABA BERSIH (NET PROFIT)</span>
                    <span>Rp {{ number_format($reportData['net_profit'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 3: AGING AR -->
    @if ($tab === 'aging-ar')
        <div class="space-y-6">
            <div class="grid gap-3 sm:grid-cols-5">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-800/30 dark:bg-emerald-950/20">
                    <p class="text-[11px] font-semibold text-emerald-800 dark:text-emerald-300">Current (0-30 Hari)</p>
                    <h4 class="mt-1 text-lg font-bold text-emerald-600">Rp {{ number_format($reportData['current'], 0, ',', '.') }}</h4>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800/30 dark:bg-amber-950/20">
                    <p class="text-[11px] font-semibold text-amber-800 dark:text-amber-300">1 - 30 Hari Overdue</p>
                    <h4 class="mt-1 text-lg font-bold text-amber-600">Rp {{ number_format($reportData['overdue_1_30'], 0, ',', '.') }}</h4>
                </div>

                <div class="rounded-xl border border-orange-200 bg-orange-50/50 p-4 dark:border-orange-800/30 dark:bg-orange-950/20">
                    <p class="text-[11px] font-semibold text-orange-800 dark:text-orange-300">31 - 60 Hari Overdue</p>
                    <h4 class="mt-1 text-lg font-bold text-orange-600">Rp {{ number_format($reportData['overdue_31_60'], 0, ',', '.') }}</h4>
                </div>

                <div class="rounded-xl border border-red-200 bg-red-50/50 p-4 dark:border-red-800/30 dark:bg-red-950/20">
                    <p class="text-[11px] font-semibold text-red-800 dark:text-red-300">61 - 90 Hari Overdue</p>
                    <h4 class="mt-1 text-lg font-bold text-red-600">Rp {{ number_format($reportData['overdue_61_90'], 0, ',', '.') }}</h4>
                </div>

                <div class="rounded-xl border border-rose-300 bg-rose-100/50 p-4 dark:border-rose-800/50 dark:bg-rose-950/30">
                    <p class="text-[11px] font-bold text-rose-900 dark:text-rose-300">> 90 Hari (Macet)</p>
                    <h4 class="mt-1 text-lg font-bold text-rose-700 dark:text-rose-400">Rp {{ number_format($reportData['overdue_90_plus'], 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Daftar Tagihan Belum Lunas Menurut Usia Piutang</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 uppercase text-gray-700 dark:bg-white/5 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-2.5">Nomor Invoice</th>
                                <th class="px-4 py-2.5">Klien</th>
                                <th class="px-4 py-2.5">Jatuh Tempo</th>
                                <th class="px-4 py-2.5 text-center">Hari Overdue</th>
                                <th class="px-4 py-2.5 text-right">Sisa Piutang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($reportData['categorized_invoices'] as $item)
                                <tr>
                                    <td class="px-4 py-2.5 font-bold text-brand-600"><a href="{{ route('invoices.show', $item['invoice']) }}" class="hover:underline">{{ $item['invoice']->number }}</a></td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">{{ $item['invoice']->client?->name }}</td>
                                    <td class="px-4 py-2.5">{{ $item['invoice']->due_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 font-bold {{ $item['days_overdue'] > 60 ? 'bg-red-100 text-red-700' : ($item['days_overdue'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                            {{ $item['days_overdue'] }} Hari
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-bold text-red-600">Rp {{ number_format($item['balance_due'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada piutang tertunggak saat ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 4: TAX SUMMARY -->
    @if ($tab === 'tax-summary')
        <div class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Dasar Pengenaan Pajak (DPP)</p>
                    <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($reportData['total_dpp'], 0, ',', '.') }}</h3>
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-5 dark:border-blue-800/30 dark:bg-blue-950/20">
                    <p class="text-xs font-medium text-blue-800 dark:text-blue-300">Total PPN Keluaran</p>
                    <h3 class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($reportData['total_ppn'], 0, ',', '.') }}</h3>
                </div>

                <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-5 dark:border-indigo-800/30 dark:bg-indigo-950/20">
                    <p class="text-xs font-medium text-indigo-800 dark:text-indigo-300">Total PPh 23 / Pemotongan</p>
                    <h3 class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($reportData['total_pph'], 0, ',', '.') }}</h3>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Rekapitulasi Faktur Pajak Invoice</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 uppercase text-gray-700 dark:bg-white/5 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-2.5">Nomor Invoice</th>
                                <th class="px-4 py-2.5">Tanggal</th>
                                <th class="px-4 py-2.5">Klien</th>
                                <th class="px-4 py-2.5 text-right">DPP</th>
                                <th class="px-4 py-2.5 text-right">PPN</th>
                                <th class="px-4 py-2.5 text-right">PPh</th>
                                <th class="px-4 py-2.5 text-right">Total Tagihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($reportData['invoices'] as $inv)
                                <tr>
                                    <td class="px-4 py-2.5 font-bold text-brand-600">{{ $inv->number }}</td>
                                    <td class="px-4 py-2.5">{{ $inv->issue_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">{{ $inv->client?->name }}</td>
                                    <td class="px-4 py-2.5 text-right font-medium">Rp {{ number_format((float)($inv->subtotal - $inv->discount_amount), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5 text-right text-blue-600 font-semibold">Rp {{ number_format((float)$inv->tax_total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5 text-right text-indigo-600 font-semibold">Rp {{ number_format((float)$inv->pph_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5 text-right font-bold text-gray-900 dark:text-white">Rp {{ number_format((float)$inv->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Tidak ada faktur pajak pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

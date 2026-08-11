<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, FinancialReportService $service)
    {
        $company = auth()->user()->company;
        $tab = $request->input('tab', 'cash-flow');

        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        $reportData = match ($tab) {
            'profit-loss' => $service->getProfitLossReport($company, $startDate, $endDate),
            'aging-ar' => $service->getAgingAccountsReceivable($company),
            'tax-summary' => $service->getTaxReport($company, $startDate, $endDate),
            default => $service->getCashFlowReport($company, $startDate, $endDate),
        };

        return view('reports.index', compact('tab', 'startDate', 'endDate', 'reportData'));
    }

    public function exportCsv(Request $request, string $type, FinancialReportService $service)
    {
        $company = auth()->user()->company;
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        $filename = "laporan-{$type}-{$company->id}-".now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($service, $company, $type, $startDate, $endDate): void {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            if ($type === 'tax-summary' || $type === 'efaktur') {
                $report = $service->getTaxReport($company, $startDate, $endDate);
                fputcsv($handle, ['LAPORAN REKAPITULASI PAJAK & E-FAKTUR']);
                fputcsv($handle, ['Perusahaan:', $company->name]);
                fputcsv($handle, ['Periode:', $startDate.' s/d '.$endDate]);
                fputcsv($handle, []);
                fputcsv($handle, ['Nomor Invoice', 'Tanggal', 'Klien', 'NPWP / Email', 'DPP (Rp)', 'PPN (Rp)', 'PPh (Rp)', 'Total Tagihan (Rp)', 'Status']);

                foreach ($report['invoices'] as $inv) {
                    fputcsv($handle, [
                        $inv->number,
                        $inv->issue_date?->format('Y-m-d'),
                        $inv->client?->name,
                        $inv->client?->email ?? '-',
                        number_format((float) ($inv->subtotal - $inv->discount_amount), 2, '.', ''),
                        number_format((float) $inv->tax_total, 2, '.', ''),
                        number_format((float) $inv->pph_amount, 2, '.', ''),
                        number_format((float) $inv->total, 2, '.', ''),
                        strtoupper($inv->status),
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['TOTAL REKAPITULASI']);
                fputcsv($handle, ['Total DPP', number_format($report['total_dpp'], 2, '.', '')]);
                fputcsv($handle, ['Total PPN Keluaran', number_format($report['total_ppn'], 2, '.', '')]);
                fputcsv($handle, ['Total PPh', number_format($report['total_pph'], 2, '.', '')]);

            } elseif ($type === 'profit-loss') {
                $report = $service->getProfitLossReport($company, $startDate, $endDate);
                fputcsv($handle, ['LAPORAN LABA RUGI (PROFIT & LOSS)']);
                fputcsv($handle, ['Perusahaan:', $company->name]);
                fputcsv($handle, ['Periode:', $startDate.' s/d '.$endDate]);
                fputcsv($handle, []);
                fputcsv($handle, ['Komponen', 'Nominal (Rp)']);
                fputcsv($handle, ['Pendapatan Kotor (Subtotal Invoice)', number_format($report['gross_revenue'], 2, '.', '')]);
                fputcsv($handle, ['Total Diskon (-)', number_format($report['total_discount'], 2, '.', '')]);
                fputcsv($handle, ['PENDAPATAN BERSIH', number_format($report['net_revenue'], 2, '.', '')]);
                fputcsv($handle, ['Total Pengeluaran & Beban (-)', number_format($report['total_expenses'], 2, '.', '')]);
                fputcsv($handle, ['Pemotongan PPh (-)', number_format($report['total_pph'], 2, '.', '')]);
                fputcsv($handle, ['LABA BERSIH (NET PROFIT)', number_format($report['net_profit'], 2, '.', '')]);

            } elseif ($type === 'aging-ar') {
                $report = $service->getAgingAccountsReceivable($company);
                fputcsv($handle, ['LAPORAN UMUR PIUTANG (AGING ACCOUNTS RECEIVABLE)']);
                fputcsv($handle, ['Perusahaan:', $company->name]);
                fputcsv($handle, ['Tanggal Cetak:', now()->format('Y-m-d H:i')]);
                fputcsv($handle, []);
                fputcsv($handle, ['Nomor Invoice', 'Klien', 'Jatuh Tempo', 'Hari Overdue', 'Kategori Usia', 'Sisa Tagihan (Rp)']);

                foreach ($report['categorized_invoices'] as $item) {
                    $inv = $item['invoice'];
                    fputcsv($handle, [
                        $inv->number,
                        $inv->client?->name,
                        $inv->due_date?->format('Y-m-d'),
                        $item['days_overdue'],
                        strtoupper($item['category']),
                        number_format($item['balance_due'], 2, '.', ''),
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['TOTAL PIUTANG', number_format($report['total_ar'], 2, '.', '')]);

            } else { // Cash Flow
                $report = $service->getCashFlowReport($company, $startDate, $endDate);
                fputcsv($handle, ['LAPORAN ARUS KAS (CASH FLOW)']);
                fputcsv($handle, ['Perusahaan:', $company->name]);
                fputcsv($handle, ['Periode:', $startDate.' s/d '.$endDate]);
                fputcsv($handle, []);
                fputcsv($handle, ['Ringkasan Arus Kas', 'Nominal (Rp)']);
                fputcsv($handle, ['Total Kas Masuk (Inflow)', number_format($report['total_inflow'], 2, '.', '')]);
                fputcsv($handle, ['Total Kas Keluar (Outflow)', number_format($report['total_outflow'], 2, '.', '')]);
                fputcsv($handle, ['ARUS KAS BERSIH (NET CASHFLOW)', number_format($report['net_cashflow'], 2, '.', '')]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function pdf(Request $request, string $type, FinancialReportService $service)
    {
        $company = auth()->user()->company;
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        $reportData = match ($type) {
            'profit-loss' => $service->getProfitLossReport($company, $startDate, $endDate),
            'aging-ar' => $service->getAgingAccountsReceivable($company),
            'tax-summary' => $service->getTaxReport($company, $startDate, $endDate),
            default => $service->getCashFlowReport($company, $startDate, $endDate),
        };

        $pdf = Pdf::loadView('pdf.report-financial', [
            'company' => $company,
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $reportData,
        ]);

        return $pdf->download("laporan-{$type}-{$company->id}.pdf");
    }
}

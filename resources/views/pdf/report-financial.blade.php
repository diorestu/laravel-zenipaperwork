<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - {{ $company->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; margin: 20px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-title { font-size: 18px; font-weight: bold; }
        .report-title { font-size: 14px; font-weight: bold; color: #444; margin-top: 5px; text-transform: uppercase; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f3f4f6; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .summary-box { background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 12px; border-radius: 6px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-title">{{ $company->name }}</div>
        <div class="report-title">LAPORAN {{ str_replace('-', ' ', strtoupper($type)) }}</div>
        <div style="font-size: 11px; color: #666;">Periode: {{ $startDate }} s/d {{ $endDate }}</div>
    </div>

    @if ($type === 'profit-loss')
        <div class="summary-box">
            <table style="width: 100%;">
                <tr><td>Pendapatan Kotor (Subtotal)</td><td class="text-right font-bold">Rp {{ number_format($data['gross_revenue'], 0, ',', '.') }}</td></tr>
                <tr><td>Total Diskon (-)</td><td class="text-right font-bold" style="color: #d97706;">-Rp {{ number_format($data['total_discount'], 0, ',', '.') }}</td></tr>
                <tr style="border-top: 1px solid #ccc;"><td class="font-bold">PENDAPATAN BERSIH</td><td class="text-right font-bold">Rp {{ number_format($data['net_revenue'], 0, ',', '.') }}</td></tr>
                <tr><td>Total Beban Pengeluaran (-)</td><td class="text-right font-bold" style="color: #dc2626;">-Rp {{ number_format($data['total_expenses'], 0, ',', '.') }}</td></tr>
                <tr><td>Potongan PPh (-)</td><td class="text-right font-bold" style="color: #dc2626;">-Rp {{ number_format($data['total_pph'], 0, ',', '.') }}</td></tr>
                <tr style="border-top: 2px solid #111; font-size: 14px;"><td class="font-bold">LABA BERSIH (NET PROFIT)</td><td class="text-right font-bold" style="color: #059669;">Rp {{ number_format($data['net_profit'], 0, ',', '.') }}</td></tr>
            </table>
        </div>
    @elseif ($type === 'aging-ar')
        <table class="table">
            <thead>
                <tr>
                    <th>Nomor Invoice</th>
                    <th>Klien</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Hari Overdue</th>
                    <th class="text-right">Sisa Piutang</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['categorized_invoices'] as $item)
                    <tr>
                        <td>{{ $item['invoice']->number }}</td>
                        <td>{{ $item['invoice']->client?->name }}</td>
                        <td>{{ $item['invoice']->due_date?->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $item['days_overdue'] }} Hari</td>
                        <td class="text-right font-bold">Rp {{ number_format($item['balance_due'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="summary-box text-right font-bold">
            Total Seluruh Piutang: Rp {{ number_format($data['total_ar'], 0, ',', '.') }}
        </div>
    @elseif ($type === 'tax-summary')
        <table class="table">
            <thead>
                <tr>
                    <th>Nomor Invoice</th>
                    <th>Tanggal</th>
                    <th>Klien</th>
                    <th class="text-right">DPP</th>
                    <th class="text-right">PPN</th>
                    <th class="text-right">PPh</th>
                    <th class="text-right">Total Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['invoices'] as $inv)
                    <tr>
                        <td>{{ $inv->number }}</td>
                        <td>{{ $inv->issue_date?->format('d/m/Y') }}</td>
                        <td>{{ $inv->client?->name }}</td>
                        <td class="text-right">Rp {{ number_format((float)($inv->subtotal - $inv->discount_amount), 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float)$inv->tax_total, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float)$inv->pph_amount, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">Rp {{ number_format((float)$inv->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="summary-box">
            <div><strong>Total DPP:</strong> Rp {{ number_format($data['total_dpp'], 0, ',', '.') }}</div>
            <div><strong>Total PPN Keluaran:</strong> Rp {{ number_format($data['total_ppn'], 0, ',', '.') }}</div>
            <div><strong>Total PPh Pemotongan:</strong> Rp {{ number_format($data['total_pph'], 0, ',', '.') }}</div>
        </div>
    @else <!-- Cash Flow -->
        <div class="summary-box">
            <table style="width: 100%;">
                <tr><td>Total Kas Masuk (Inflow)</td><td class="text-right font-bold" style="color: #059669;">Rp {{ number_format($data['total_inflow'], 0, ',', '.') }}</td></tr>
                <tr><td>Total Kas Keluar (Outflow)</td><td class="text-right font-bold" style="color: #dc2626;">Rp {{ number_format($data['total_outflow'], 0, ',', '.') }}</td></tr>
                <tr style="border-top: 2px solid #111; font-size: 14px;"><td class="font-bold">ARUS KAS BERSIH</td><td class="text-right font-bold">Rp {{ number_format($data['net_cashflow'], 0, ',', '.') }}</td></tr>
            </table>
        </div>
    @endif

    <div style="margin-top: 40px; font-size: 10px; color: #888; text-align: center;">
        Dicetak secara otomatis dari Sistem Paperwork pada {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>

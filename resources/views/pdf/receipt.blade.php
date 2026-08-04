<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}.right{text-align:right}table{width:100%;border-collapse:collapse}td,th{border-bottom:1px solid #ddd;padding:8px}</style></head>
<body>
<h1>Kuitansi {{ $invoice->number }}</h1>
<p>{{ $invoice->company->name }}<br>{{ $invoice->client->name }}</p>
<table><thead><tr><th>Tanggal</th><th>Termin</th><th>Metode</th><th>Referensi</th><th class="right">Jumlah</th></tr></thead><tbody>
@foreach($invoice->payments as $payment)<tr><td>{{ $payment->paid_at->format('d M Y') }}</td><td>{{ $payment->term_label ?: '-' }}</td><td>{{ $payment->method }}</td><td>{{ $payment->reference }}</td><td class="right">{{ number_format($payment->amount, 0, ',', '.') }}</td></tr>@endforeach
</tbody></table>
<h3 class="right">Total Terbayar Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}</h3>
<p class="right">Sisa Tagihan Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}</p>
</body></html>

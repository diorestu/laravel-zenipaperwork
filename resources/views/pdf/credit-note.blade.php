<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}.right{text-align:right}table{width:100%;border-collapse:collapse}td,th{border-bottom:1px solid #ddd;padding:8px}</style></head>
<body>
<h1>Credit Note {{ $note->number }}</h1>
<p><strong>{{ $note->company->name }}</strong><br>
Issued to: {{ $note->client->name }}<br>
Reference Invoice: {{ $note->invoice->number }}<br>
Issue Date: {{ $note->issue_date->format('d M Y') }}<br>
Status: {{ str($note->status)->headline() }}</p>
<table>
    <tr><th>Reason</th><td>{{ $note->reason }}</td></tr>
    @if($note->notes)<tr><th>Notes</th><td>{{ $note->notes }}</td></tr>@endif
    <tr><th>Amount</th><td class="right"><strong>Rp {{ number_format($note->amount, 0, ',', '.') }}</strong></td></tr>
</table>
</body></html>

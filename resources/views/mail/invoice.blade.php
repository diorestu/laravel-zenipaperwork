<p>Halo {{ $invoice->client->name }},</p>
<p>Invoice {{ $invoice->number }} senilai Rp {{ number_format($invoice->total, 0, ',', '.') }} sudah tersedia.</p>
<p>Buka invoice: <a href="{{ route('public.invoices.show', $invoice->public_token) }}">{{ route('public.invoices.show', $invoice->public_token) }}</a></p>

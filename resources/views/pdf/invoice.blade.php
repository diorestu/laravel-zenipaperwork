<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700;800;900&display=swap');

        @page {
            margin: 40px 45px;
        }

        body, h1, h2, h3, h4, h5, h6, table, th, td, span, div, p {
            font-family: 'Barlow', Helvetica, Arial, sans-serif !important;
        }
        
        body {
            font-size: 12px;
            color: #111111;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        
        .header-table, .client-table, .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .header-table td, .client-table td, .footer-table td {
            padding: 0;
            vertical-align: top;
        }
        
        .text-right {
            text-align: right;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: 600;
            margin-top: 5px;
            margin-bottom: 2px;
        }
        
        .company-address {
            color: #6b7280;
            max-width: 280px;
            line-height: 1.25;
        }
        
        .company-contact {
            color: #9ca3af;
            font-size: 11px;
            margin-top: 2px;
        }
        
        .doc-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 2px 0;
            color: #111111;
        }
        
        .doc-number {
            font-size: 12px;
            font-weight: 500;
            color: #4f46e5;
            margin: 0 0 6px 0;
        }
        
        .metadata-label {
            color: #9ca3af;
        }
        
        .metadata-value {
            font-weight: 500;
            color: #374151;
        }
        
        .section-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 2px;
        }
        
        .client-name {
            font-size: 13px;
            font-weight: 700;
            color: #111111;
            margin: 0 0 1px 0;
        }
        
        .client-company {
            font-size: 12px;
            font-weight: 500;
            color: #4b5563;
            margin: 0 0 2px 0;
        }
        
        .client-address {
            color: #6b7280;
            max-width: 320px;
            line-height: 1.25;
        }
        
        .client-contact {
            color: #9ca3af;
            font-size: 11px;
            margin-top: 3px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .terms-container {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 15px;
        }

        .terms-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .terms-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table th,
        .terms-table th {
            border-bottom: 1px solid #e2e8f0;
            padding: 4px 6px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
        }
        
        .items-table td,
        .terms-table td {
            border-bottom: 1px solid #f1f5f9;
            padding: 5px 6px;
            vertical-align: top;
        }

        .terms-table tr:last-child td {
            border-bottom: none;
        }

        .term-label {
            font-weight: 700;
            color: #0f172a;
        }

        .term-note {
            font-size: 10px;
            color: #64748b;
        }
        
        .item-description {
            font-size: 11px;
            color: #111111;
        }
        
        .item-description-name {
            font-weight: 700;
            font-size: 11px;
            color: #111111;
            line-height: 1.3;
        }

        .item-description-text {
            font-weight: 400;
            font-size: 10px;
            color: #4b5563;
            line-height: 1.4;
            margin-top: 2px;
        }

        .item-line-total {
            font-weight: 700;
        }
        
        .totals-table {
            width: 260px;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 3px 0;
            font-size: 12px;
        }
        
        .totals-label {
            color: #6b7280;
        }
        
        .totals-val {
            text-align: right;
            font-weight: 500;
            color: #111111;
        }
        
        .totals-grand {
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            font-weight: 700;
            padding-top: 4px;
        }

        .totals-grand td,
        .totals-balance td {
            font-weight: 700;
        }

        .totals-balance {
            border-top: 1px solid #111111;
            font-size: 14px;
            font-weight: 700;
            padding-top: 4px;
        }
        
        .notes-container {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 10px 12px;
            max-width: 380px;
        }
        
        .notes-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        
        .notes-content {
            font-size: 11px;
            color: #334155;
            line-height: 1.4;
        }

        .watermark {
            position: fixed;
            top: 38%;
            left: 5%;
            width: 90%;
            text-align: center;
            opacity: 0.12;
            font-size: 46px;
            font-weight: 800;
            color: #000000;
            transform: rotate(-30deg);
            transform-origin: 50% 50%;
            z-index: -1000;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @if ($invoice->company?->getActivePlanSlug() === 'trial')
        <div class="watermark">PAPERWORK TRIAL</div>
    @elseif ($invoice->company?->getActivePlanSlug() === 'free')
        <div class="watermark">PAPERWORK FREE</div>
    @endif

    <!-- Top Header: Logo & Company Info vs Doc Title & Metadata -->
    <table class="header-table">
        <tr>
            <!-- Left: Company Logo & Details -->
            <td>
                @if ($invoice->company->logo_path && file_exists(storage_path('app/public/' . $invoice->company->logo_path)))
                    <img src="{{ storage_path('app/public/' . $invoice->company->logo_path) }}" style="height: 56px; max-width: 200px; object-fit: contain;">
                @else
                    <div style="background-color: #f3f4f6; color: #374151; font-weight: bold; font-size: 18px; width: 56px; height: 56px; line-height: 56px; text-align: center; border-radius: 8px; text-transform: uppercase;">
                        {{ substr($invoice->company->name, 0, 2) }}
                    </div>
                @endif
                
                <div class="company-name">{{ $invoice->company->name }}</div>
                @if ($invoice->company->address)
                    <div class="company-address">{{ $invoice->company->address }}</div>
                @endif
                @if ($invoice->company->email || $invoice->company->phone)
                    <div class="company-contact">
                        {{ $invoice->company->email }}{{ $invoice->company->email && $invoice->company->phone ? ' | ' : '' }}{{ $invoice->company->phone }}
                    </div>
                @endif
            </td>
            
            <!-- Right: Doc Title & Metadata -->
            <td class="text-right" style="width: 280px;">
                <h1 class="doc-title">INVOICE TAGIHAN</h1>
                <div class="doc-number">{{ $invoice->number }}</div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="text-right metadata-label" style="padding: 2px 0; font-size: 11px;">Tanggal:</td>
                        <td class="text-right metadata-value" style="padding: 2px 0; width: 120px; font-size: 11px;">
                            <x-date-display :date="$invoice->issue_date" />
                        </td>
                    </tr>
                    @if ($invoice->due_date)
                        <tr>
                            <td class="text-right metadata-label" style="padding: 2px 0; font-size: 11px;">Jatuh Tempo:</td>
                            <td class="text-right metadata-value" style="padding: 2px 0; font-size: 11px;">
                                <x-date-display :date="$invoice->due_date" />
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Client Info -->
    <table class="client-table">
        <tr>
            <td>
                <div class="section-title">Ditujukan Kepada</div>
                <h4 class="client-name">{{ $invoice->client->name }}</h4>
                @if ($invoice->client->company_name)
                    <div class="client-company">{{ $invoice->client->company_name }}</div>
                @endif
                @if ($invoice->client->address)
                    <div class="client-address">{{ $invoice->client->address }}</div>
                @endif
                @if ($invoice->client->email || $invoice->client->phone)
                    <div class="client-contact">
                        {{ $invoice->client->email }}{{ $invoice->client->email && $invoice->client->phone ? ' | ' : '' }}{{ $invoice->client->phone }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="text-align: left;">Item</th>
                <th class="text-right" style="width: 50px;">Qty</th>
                <th class="text-right" style="width: 100px;">Harga</th>
                <th class="text-right" style="width: 110px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                @php
                    $itemName = '';
                    $itemDesc = '';

                    if ($item->product) {
                        $itemName = $item->product->name;
                        $rawDesc = trim($item->description ?? '');
                        if ($rawDesc !== '' && strtolower($rawDesc) !== strtolower($item->product->name)) {
                            $cleaned = preg_replace('/^'.preg_quote($item->product->name, '/').'\s*[-:\n]?\s*/i', '', $rawDesc);
                            $itemDesc = trim($cleaned);
                        }
                    } else {
                        $fullDesc = trim($item->description ?? '');
                        if (str_contains($fullDesc, "\n")) {
                            $lines = array_filter(array_map('trim', explode("\n", $fullDesc)));
                            $itemName = array_shift($lines) ?? $fullDesc;
                            $itemDesc = implode("\n", $lines);
                        } elseif (str_contains($fullDesc, ' - ')) {
                            $parts = explode(' - ', $fullDesc, 2);
                            $itemName = trim($parts[0]);
                            $itemDesc = trim($parts[1]);
                        } elseif (str_contains($fullDesc, ' – ')) {
                            $parts = explode(' – ', $fullDesc, 2);
                            $itemName = trim($parts[0]);
                            $itemDesc = trim($parts[1]);
                        } else {
                            $itemName = $fullDesc;
                            $itemDesc = '';
                        }
                    }

                    if (empty($itemName)) {
                        $itemName = $item->description ?? 'Item';
                    }
                @endphp
                <tr>
                    <td class="item-description">
                        <div class="item-description-name">{{ $itemName }}</div>
                        @if ($itemDesc !== '')
                            <div class="item-description-text">{!! nl2br(e($itemDesc)) !!}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format((float) $item->quantity, 0, ',', '.') }}</td>
                    <td class="text-right"><x-money :amount="$item->unit_price" /></td>
                    <td class="text-right item-line-total"><x-money :amount="$item->line_total" /></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($invoice->paymentTerms->isNotEmpty())
        <div class="terms-container">
            <div class="terms-title">Termin Pembayaran</div>
            <table class="terms-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Termin</th>
                        <th style="text-align: left; width: 120px;">Jatuh Tempo</th>
                        <th class="text-right" style="width: 130px;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->paymentTerms as $term)
                        <tr>
                            <td>
                                <div class="term-label">{{ $term->label }}</div>
                                <div class="term-note">Termin {{ $term->term_number }}</div>
                            </td>
                            <td>{{ $term->due_date ? $term->due_date->format('d M Y') : '-' }}</td>
                            <td class="text-right item-line-total"><x-money :amount="$term->amount" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Summary & Notes -->
    <table class="footer-table">
        <tr>
            <!-- Notes -->
            <td>
                @php
                    $notes = trim((string) $invoice->notes);
                    $selectedBank = $invoice->bankAccount;

                    if ($selectedBank) {
                        $singleBankText = "- {$selectedBank->bank_name} a/n {$selectedBank->account_name} ({$selectedBank->account_number})";
                        if (preg_match('/Pembayaran dapat ditransfer melalui rekening berikut:[\s\S]*?(?=\n\n|\r\n\r\n|$)/i', $notes, $matches)) {
                            $notes = str_replace($matches[0], "Pembayaran dapat ditransfer melalui rekening berikut:\n" . $singleBankText, $notes);
                        } elseif (!str_contains($notes, $selectedBank->account_number)) {
                            $notes .= ($notes !== '' ? "\n\n" : '') . "Pembayaran dapat ditransfer melalui rekening berikut:\n" . $singleBankText;
                        }
                    } else {
                        $hasBankInNotes = preg_match('/(?:rekening|transfer|a\/n\s+[^\n]+|\d{5,})/i', $notes);
                        if (!$hasBankInNotes) {
                            $bankAccounts = $invoice->company?->bankAccounts ?? collect();
                            if ($bankAccounts->isNotEmpty()) {
                                $notes .= ($notes !== '' ? "\n\n" : '') . "Pembayaran dapat ditransfer melalui rekening berikut:\n";
                                foreach ($bankAccounts as $account) {
                                    $notes .= "- {$account->bank_name} a/n {$account->account_name} ({$account->account_number})\n";
                                }
                            }
                        }
                    }
                    $notes = trim($notes);
                @endphp

                @if ($notes !== '')
                    <div class="notes-container">
                        <div class="notes-title">Catatan</div>
                        <div class="notes-content">{!! nl2br(e($notes)) !!}</div>
                    </div>
                @endif
            </td>
            
            <!-- Totals breakdown -->
            <td style="width: 270px;">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Subtotal</td>
                        <td class="totals-val"><x-money :amount="$invoice->subtotal" /></td>
                    </tr>
                    @if ((float) $invoice->discount_amount > 0)
                        <tr>
                            <td class="totals-label">Diskon {{ $invoice->discount_type === 'percentage' && $invoice->discount_rate > 0 ? '('.(float)$invoice->discount_rate.'%)' : '' }}</td>
                            <td class="totals-val">(<x-money :amount="$invoice->discount_amount" />)</td>
                        </tr>
                    @endif
                    @foreach ($invoice->normalized_custom_taxes as $tax)
                        @if ($tax['rate'] > 0 || $tax['amount'] > 0)
                            <tr>
                                <td class="totals-label">{{ $tax['name'] }} ({{ $tax['rate'] }}%)</td>
                                <td class="totals-val">
                                    @if ($tax['type'] === 'deduction')
                                        (<x-money :amount="$tax['amount']" />)
                                    @else
                                        <x-money :amount="$tax['amount']" />
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @if ((float) $invoice->down_payment_amount > 0)
                        <tr>
                            <td class="totals-label">Uang Muka (DP)</td>
                            <td class="totals-val"><x-money :amount="$invoice->down_payment_amount" /></td>
                        </tr>
                    @endif
                    
                    <tr class="totals-grand">
                        <td class="totals-label">Total Tagihan</td>
                        <td class="totals-val"><x-money :amount="$invoice->total" /></td>
                    </tr>
                    
                    <tr>
                        <td class="totals-label">Total Terbayar</td>
                        <td class="totals-val" style="color: #16a34a;"><x-money :amount="$invoice->amount_paid" /></td>
                    </tr>

                    @if ((float) $invoice->credit_note_total > 0)
                        <tr>
                            <td class="totals-label">Nota Kredit</td>
                            <td class="totals-val" style="color: #4f46e5;"><x-money :amount="$invoice->credit_note_total" /></td>
                        </tr>
                    @endif
                    
                    <tr class="totals-balance">
                        <td class="totals-label">Sisa Tagihan</td>
                        <td class="totals-val"><x-money :amount="$invoice->balance_due" /></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

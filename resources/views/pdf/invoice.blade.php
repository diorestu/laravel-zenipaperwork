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
        
        .items-table th {
            border-bottom: 1px solid #e5e7eb;
            padding: 4px 0;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #9ca3af;
        }
        
        .items-table td {
            border-bottom: 1px solid #f3f4f6;
            padding: 4px 0;
            vertical-align: top;
        }
        
        .item-description {
            font-size: 11px;
            color: #111111;
        }
        
        .item-description-line {
            display: block;
            margin-bottom: 1px;
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
            font-weight: 600;
            padding-top: 4px;
        }

        .totals-balance {
            border-top: 1px solid #111111;
            font-size: 14px;
            font-weight: 700;
            padding-top: 4px;
        }
        
        .notes-container {
            max-width: 400px;
        }
        
        .notes-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        
        .notes-content {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.3;
        }
    </style>
</head>
<body>
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
                    $parts = array_filter(array_map('trim', explode('-', $item->description)));
                @endphp
                <tr>
                    <td class="item-description">
                        @foreach ($parts as $part)
                            <span class="item-description-line">{{ $part }}</span>
                        @endforeach
                    </td>
                    <td class="text-right">{{ number_format((float) $item->quantity, 2) }}</td>
                    <td class="text-right"><x-money :amount="$item->unit_price" /></td>
                    <td class="text-right"><x-money :amount="$item->line_total" /></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary & Notes -->
    <table class="footer-table">
        <tr>
            <!-- Notes -->
            <td>
                @if ($invoice->notes)
                    <div class="notes-container">
                        <div class="notes-title">Catatan</div>
                        <div class="notes-content">{!! nl2br(e($invoice->notes)) !!}</div>
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
                    @if ($invoice->tax_rate > 0)
                        <tr>
                            <td class="totals-label">Pajak ({{ $invoice->tax_rate }}%)</td>
                            <td class="totals-val"><x-money :amount="$invoice->tax_total" /></td>
                        </tr>
                    @endif
                    @if ((float) $invoice->down_payment_amount > 0)
                        <tr>
                            <td class="totals-label">Uang Muka (DP)</td>
                            <td class="totals-val"><x-money :amount="$invoice->down_payment_amount" /></td>
                        </tr>
                    @endif
                    
                    <tr class="totals-grand">
                        <td class="totals-label font-semibold">Total Tagihan</td>
                        <td class="totals-val font-semibold"><x-money :amount="$invoice->total" /></td>
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
                        <td class="totals-label font-bold">Sisa Tagihan</td>
                        <td class="totals-val font-bold"><x-money :amount="$invoice->balance_due" /></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

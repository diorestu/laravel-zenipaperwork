<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penawaran {{ $quotation->number }}</title>
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
            width: 240px;
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
            font-size: 14px;
            font-weight: 700;
            padding-top: 4px;
        }

        .totals-grand td {
            font-weight: 700;
        }
        
        .notes-container {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            background-color: #fafafa;
            max-width: 400px;
        }
        
        .notes-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #111111;
            margin-bottom: 4px;
        }
        
        .notes-content {
            font-size: 11px;
            color: #4b5563;
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
    @if ($quotation->company?->getActivePlanSlug() === 'trial')
        <div class="watermark">PAPERWORK TRIAL</div>
    @elseif ($quotation->company?->getActivePlanSlug() === 'free')
        <div class="watermark">PAPERWORK FREE</div>
    @endif

    <!-- Top Header: Logo & Company Info vs Doc Title & Metadata -->
    <table class="header-table">
        <tr>
            <!-- Left: Company Logo & Details -->
            <td>
                @if ($quotation->company->logo_path && file_exists(storage_path('app/public/' . $quotation->company->logo_path)))
                    <img src="{{ storage_path('app/public/' . $quotation->company->logo_path) }}" style="height: 56px; max-width: 200px; object-fit: contain;">
                @else
                    <div style="background-color: #f3f4f6; color: #374151; font-weight: bold; font-size: 18px; width: 56px; height: 56px; line-height: 56px; text-align: center; border-radius: 8px; text-transform: uppercase;">
                        {{ substr($quotation->company->name, 0, 2) }}
                    </div>
                @endif
                
                <div class="company-name">{{ $quotation->company->name }}</div>
                @if ($quotation->company->address)
                    <div class="company-address">{{ $quotation->company->address }}</div>
                @endif
                @if ($quotation->company->email || $quotation->company->phone)
                    <div class="company-contact">
                        {{ $quotation->company->email }}{{ $quotation->company->email && $quotation->company->phone ? ' | ' : '' }}{{ $quotation->company->phone }}
                    </div>
                @endif
            </td>
            
            <!-- Right: Doc Title & Metadata -->
            <td class="text-right" style="width: 280px;">
                <h1 class="doc-title">SURAT PENAWARAN</h1>
                <div class="doc-number">{{ $quotation->number }}</div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="text-right metadata-label" style="padding: 2px 0; font-size: 11px;">Tanggal:</td>
                        <td class="text-right metadata-value" style="padding: 2px 0; width: 120px; font-size: 11px;">
                            <x-date-display :date="$quotation->issue_date" />
                        </td>
                    </tr>
                    @if ($quotation->valid_until)
                        <tr>
                            <td class="text-right metadata-label" style="padding: 2px 0; font-size: 11px;">Berlaku Hingga:</td>
                            <td class="text-right metadata-value" style="padding: 2px 0; font-size: 11px;">
                                <x-date-display :date="$quotation->valid_until" />
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
                <h4 class="client-name">{{ $quotation->client->name }}</h4>
                @if ($quotation->client->company_name)
                    <div class="client-company">{{ $quotation->client->company_name }}</div>
                @endif
                @if ($quotation->client->address)
                    <div class="client-address">{{ $quotation->client->address }}</div>
                @endif
                @if ($quotation->client->email || $quotation->client->phone)
                    <div class="client-contact">
                        {{ $quotation->client->email }}{{ $quotation->client->email && $quotation->client->phone ? ' | ' : '' }}{{ $quotation->client->phone }}
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
            @foreach ($quotation->items as $item)
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

    <!-- Summary & Notes -->
    <table class="footer-table">
        <tr>
            <!-- Notes -->
            <td>
                @if ($quotation->notes)
                    <div class="notes-container">
                        <div class="notes-title">Catatan</div>
                        <div class="notes-content">{!! nl2br(e($quotation->notes)) !!}</div>
                    </div>
                @endif
            </td>
            
            <!-- Totals breakdown -->
            <td style="width: 250px;">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Subtotal</td>
                        <td class="totals-val"><x-money :amount="$quotation->subtotal" /></td>
                    </tr>
                    @foreach ($quotation->normalized_custom_taxes as $tax)
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
                    <tr class="totals-grand">
                        <td class="totals-label">Total</td>
                        <td class="totals-val"><x-money :amount="$quotation->total" /></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

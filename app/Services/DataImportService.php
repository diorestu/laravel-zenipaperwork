<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataImportService
{
    /**
     * Import JSON backup data into specified company.
     *
     * @param array $data Parsed JSON backup array
     * @param Company $company Target company model
     * @return array Summary of imported counts
     */
    public function import(array $data, Company $company): array
    {
        return DB::transaction(function () use ($data, $company): array {
            $stats = [
                'products' => 0,
                'clients' => 0,
                'quotations' => 0,
                'invoices' => 0,
            ];

            $productIdMap = [];
            $clientIdMap = [];

            // 1. Import Products
            if (! empty($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $item) {
                    $oldId = $item['id'] ?? null;
                    $name = trim($item['name'] ?? '');

                    if (! $name) {
                        continue;
                    }

                    $product = Product::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'name' => $name,
                        ],
                        [
                            'description' => $item['description'] ?? null,
                            'price' => (float) ($item['price'] ?? 0),
                            'unit' => 'unit',
                            'is_active' => true,
                        ]
                    );

                    if ($oldId) {
                        $productIdMap[$oldId] = $product->id;
                    }

                    $stats['products']++;
                }
            }

            // 2. Import Clients
            if (! empty($data['clients']) && is_array($data['clients'])) {
                foreach ($data['clients'] as $item) {
                    $oldId = $item['id'] ?? null;
                    $name = trim($item['name'] ?? '');

                    if (! $name) {
                        continue;
                    }

                    $companyName = ! empty($item['company']) && $item['company'] !== '-' ? trim($item['company']) : null;
                    $email = ! empty($item['email']) && filter_var($item['email'], FILTER_VALIDATE_EMAIL) ? trim($item['email']) : null;

                    $client = Client::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'name' => $name,
                        ],
                        [
                            'company_name' => $companyName,
                            'email' => $email,
                            'phone' => $item['phone'] ?? null,
                            'address' => $item['address'] ?? null,
                        ]
                    );

                    if ($oldId) {
                        $clientIdMap[$oldId] = $client->id;
                    }

                    $stats['clients']++;
                }
            }

            // 3. Import Quotations
            if (! empty($data['quotations']) && is_array($data['quotations'])) {
                foreach ($data['quotations'] as $item) {
                    $number = trim($item['quotation_number'] ?? $item['number'] ?? '');
                    if (! $number) {
                        continue;
                    }

                    $oldClientId = $item['client_id'] ?? ($item['client']['id'] ?? null);
                    $clientId = $oldClientId && isset($clientIdMap[$oldClientId]) ? $clientIdMap[$oldClientId] : null;

                    if (! $clientId && ! empty($item['client']['name'])) {
                        $client = Client::where('company_id', $company->id)
                            ->where('name', trim($item['client']['name']))
                            ->first();
                        $clientId = $client?->id;
                    }

                    if (! $clientId) {
                        // Fallback: pick first client if available
                        $clientId = Client::where('company_id', $company->id)->first()?->id;
                    }

                    if (! $clientId) {
                        continue;
                    }

                    $status = match ($item['status'] ?? 'draft') {
                        'accepted', 'approved' => 'approved',
                        'declined', 'rejected' => 'rejected',
                        'sent' => 'sent',
                        default => 'draft',
                    };

                    $subtotal = (float) ($item['subtotal'] ?? 0);
                    $total = (float) ($item['total'] ?? $subtotal);

                    $quotation = Quotation::updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'number' => $number,
                        ],
                        [
                            'client_id' => $clientId,
                            'issue_date' => $item['quotation_date'] ?? $item['issue_date'] ?? now()->toDateString(),
                            'valid_until' => $item['valid_until'] ?? null,
                            'status' => $status,
                            'subtotal' => $subtotal,
                            'tax_rate' => 0,
                            'tax_total' => (float) ($item['tax_total'] ?? 0),
                            'total' => $total,
                            'notes' => $item['notes'] ?? null,
                        ]
                    );

                    // Import items
                    if (! empty($item['items']) && is_array($item['items'])) {
                        $quotation->items()->delete();
                        foreach ($item['items'] as $quotItem) {
                            $oldProdId = $quotItem['product_id'] ?? null;
                            $productId = $oldProdId && isset($productIdMap[$oldProdId]) ? $productIdMap[$oldProdId] : null;

                            QuotationItem::create([
                                'quotation_id' => $quotation->id,
                                'product_id' => $productId,
                                'description' => $quotItem['description'] ?? 'Item',
                                'quantity' => (float) ($quotItem['quantity'] ?? 1),
                                'unit_price' => (float) ($quotItem['unit_price'] ?? 0),
                                'line_total' => (float) ($quotItem['subtotal'] ?? $quotItem['line_total'] ?? 0),
                            ]);
                        }
                    }

                    $stats['quotations']++;
                }
            }

            // 4. Import Invoices
            if (! empty($data['invoices']) && is_array($data['invoices'])) {
                foreach ($data['invoices'] as $item) {
                    $number = trim($item['invoice_number'] ?? $item['number'] ?? '');
                    if (! $number) {
                        continue;
                    }

                    $oldClientId = $item['client_id'] ?? ($item['client']['id'] ?? null);
                    $clientId = $oldClientId && isset($clientIdMap[$oldClientId]) ? $clientIdMap[$oldClientId] : null;

                    if (! $clientId && ! empty($item['client']['name'])) {
                        $client = Client::where('company_id', $company->id)
                            ->where('name', trim($item['client']['name']))
                            ->first();
                        $clientId = $client?->id;
                    }

                    if (! $clientId) {
                        $clientId = Client::where('company_id', $company->id)->first()?->id;
                    }

                    if (! $clientId) {
                        continue;
                    }

                    $status = match ($item['status'] ?? 'draft') {
                        'paid' => 'paid',
                        'partial' => 'partial',
                        'sent' => 'sent',
                        'void' => 'void',
                        default => 'draft',
                    };

                    $subtotal = (float) ($item['subtotal'] ?? 0);
                    $total = (float) ($item['total'] ?? $subtotal);
                    $amountPaid = $status === 'paid' ? $total : ($status === 'partial' ? $total / 2 : 0);
                    $balanceDue = max(0, $total - $amountPaid);

                    // Normalize applied taxes
                    $customTaxes = [];
                    if (! empty($item['applied_taxes']) && is_array($item['applied_taxes'])) {
                        foreach ($item['applied_taxes'] as $tax) {
                            $type = ($tax['type'] ?? '') === 'subtract' ? 'deduction' : 'addition';
                            $customTaxes[] = [
                                'name' => $tax['name'] ?? 'Pajak',
                                'rate' => abs((float) ($tax['rate'] ?? 0)),
                                'type' => $type,
                            ];
                        }
                    }

                    $invoice = Invoice::updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'number' => $number,
                        ],
                        [
                            'client_id' => $clientId,
                            'public_token' => Str::ulid()->toBase32(),
                            'issue_date' => $item['invoice_date'] ?? $item['issue_date'] ?? now()->toDateString(),
                            'due_date' => $item['due_date'] ?? null,
                            'status' => $status,
                            'subtotal' => $subtotal,
                            'tax_rate' => 0,
                            'tax_total' => (float) ($item['tax_total'] ?? 0),
                            'custom_taxes' => $customTaxes,
                            'total' => $total,
                            'amount_paid' => $amountPaid,
                            'balance_due' => $balanceDue,
                            'notes' => $item['notes'] ?? null,
                        ]
                    );

                    // Import items
                    if (! empty($item['items']) && is_array($item['items'])) {
                        $invoice->items()->delete();
                        foreach ($item['items'] as $invItem) {
                            $oldProdId = $invItem['product_id'] ?? null;
                            $productId = $oldProdId && isset($productIdMap[$oldProdId]) ? $productIdMap[$oldProdId] : null;

                            InvoiceItem::create([
                                'invoice_id' => $invoice->id,
                                'product_id' => $productId,
                                'description' => $invItem['description'] ?? 'Item',
                                'quantity' => (float) ($invItem['quantity'] ?? 1),
                                'unit_price' => (float) ($invItem['unit_price'] ?? 0),
                                'line_total' => (float) ($invItem['subtotal'] ?? $invItem['line_total'] ?? 0),
                            ]);
                        }
                    }

                    $stats['invoices']++;
                }
            }

            return $stats;
        });
    }

    /**
     * Export all data of specified company to JSON array.
     */
    public function export(Company $company, ?User $user = null): array
    {
        $products = Product::where('company_id', $company->id)->get();
        $clients = Client::where('company_id', $company->id)->get();
        $quotations = Quotation::where('company_id', $company->id)->with(['client', 'items'])->get();
        $invoices = Invoice::where('company_id', $company->id)->with(['client', 'items'])->get();

        return [
            'exported_at' => now()->toIso8601String(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_id' => $company->id,
            ] : null,
            'products' => $products->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => number_format((float) $p->price, 2, '.', ''),
                'description' => $p->description,
                'created_at' => $p->created_at?->toISOString(),
            ])->toArray(),
            'clients' => $clients->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'company' => $c->company_name,
                'address' => $c->address,
                'created_at' => $c->created_at?->toISOString(),
            ])->toArray(),
            'quotations' => $quotations->map(fn ($q) => [
                'id' => $q->id,
                'quotation_number' => $q->number,
                'quotation_date' => $q->issue_date?->toDateString(),
                'valid_until' => $q->valid_until?->toDateString(),
                'status' => $q->status,
                'subtotal' => number_format((float) $q->subtotal, 2, '.', ''),
                'tax_total' => number_format((float) $q->tax_total, 2, '.', ''),
                'total' => number_format((float) $q->total, 2, '.', ''),
                'notes' => $q->notes,
                'client' => $q->client ? [
                    'id' => $q->client->id,
                    'name' => $q->client->name,
                    'email' => $q->client->email,
                ] : null,
                'items' => $q->items->map(fn ($i) => [
                    'id' => $i->id,
                    'product_id' => $i->product_id,
                    'description' => $i->description,
                    'quantity' => (float) $i->quantity,
                    'unit_price' => number_format((float) $i->unit_price, 2, '.', ''),
                    'subtotal' => number_format((float) $i->line_total, 2, '.', ''),
                ])->toArray(),
            ])->toArray(),
            'invoices' => $invoices->map(fn ($i) => [
                'id' => $i->id,
                'invoice_number' => $i->number,
                'invoice_date' => $i->issue_date?->toDateString(),
                'due_date' => $i->due_date?->toDateString(),
                'status' => $i->status,
                'subtotal' => number_format((float) $i->subtotal, 2, '.', ''),
                'tax_total' => number_format((float) $i->tax_total, 2, '.', ''),
                'total' => number_format((float) $i->total, 2, '.', ''),
                'applied_taxes' => $i->custom_taxes,
                'notes' => $i->notes,
                'client' => $i->client ? [
                    'id' => $i->client->id,
                    'name' => $i->client->name,
                    'email' => $i->client->email,
                ] : null,
                'items' => $i->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => number_format((float) $item->unit_price, 2, '.', ''),
                    'subtotal' => number_format((float) $item->line_total, 2, '.', ''),
                ])->toArray(),
            ])->toArray(),
        ];
    }
}

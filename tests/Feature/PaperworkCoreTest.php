<?php

use App\Models\BillingSubmission;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function paperworkUser(string $role = 'owner'): User
{
    $company = Company::factory()->create();

    return User::factory()->create([
        'company_id' => $company->id,
        'role' => $role,
    ]);
}

it('creates and shows an invoice with computed totals', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $product = Product::factory()->for($user->company)->create(['price' => 150000]);

    $response = $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'number' => 'INV-001',
        'issue_date' => '2026-06-22',
        'due_date' => '2026-06-29',
        'tax_rate' => 11,
        'items' => [
            ['product_id' => $product->id, 'description' => 'Brand kit', 'quantity' => 2, 'unit_price' => 150000],
        ],
    ]);

    $invoice = Invoice::first();

    $response->assertRedirect(route('invoices.show', $invoice));
    expect($invoice->subtotal)->toBe('300000.00');
    expect($invoice->tax_total)->toBe('33000.00');
    expect($invoice->total)->toBe('333000.00');
    expect($invoice->amount_paid)->toBe('0.00');
    expect($invoice->balance_due)->toBe('333000.00');

    $this->actingAs($user)
        ->get(route('invoices.show', $invoice))
        ->assertOk()
        ->assertSee('INV-001')
        ->assertSee('Brand kit');
});

it('records invoice payments and derives partial and paid status', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $invoice = Invoice::factory()->for($user->company)->for($client)->create(['total' => 500000, 'status' => 'sent']);

    $this->actingAs($user)->post(route('invoices.payments.store', $invoice), [
        'amount' => 200000,
        'paid_at' => '2026-06-22',
        'method' => 'bank_transfer',
        'reference' => 'TRX-001',
    ])->assertRedirect(route('invoices.show', $invoice));

    $invoice->refresh();
    expect($invoice->amount_paid)->toBe('200000.00');
    expect($invoice->balance_due)->toBe('300000.00');
    expect($invoice->status)->toBe('partial');

    $this->actingAs($user)->post(route('invoices.payments.store', $invoice), [
        'amount' => 300000,
        'paid_at' => '2026-06-23',
        'method' => 'bank_transfer',
        'reference' => 'TRX-002',
    ])->assertRedirect(route('invoices.show', $invoice));

    $invoice->refresh();
    expect($invoice->amount_paid)->toBe('500000.00');
    expect($invoice->balance_due)->toBe('0.00');
    expect($invoice->status)->toBe('paid');
});

it('converts an approved quotation into an invoice', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $quotation = Quotation::factory()->for($user->company)->for($client)->create([
        'number' => 'QUO-001',
        'status' => 'approved',
        'subtotal' => 250000,
        'tax_rate' => 0,
        'tax_total' => 0,
        'total' => 250000,
    ]);
    $quotation->items()->create([
        'description' => 'Monthly paperwork',
        'quantity' => 1,
        'unit_price' => 250000,
        'line_total' => 250000,
    ]);

    $this->actingAs($user)
        ->post(route('quotations.convert', $quotation), ['number' => 'INV-Q-001'])
        ->assertRedirect();

    $invoice = Invoice::where('number', 'INV-Q-001')->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->quotation_id)->toBe($quotation->id);
    expect($invoice->items)->toHaveCount(1);
});

it('accepts manual billing submissions with proof files', function () {
    Storage::fake('public');
    $user = paperworkUser();

    $this->actingAs($user)->post(route('billing.store'), [
        'package' => 'business',
        'billing_period' => 'monthly',
        'amount' => 149000,
        'payment_method' => 'manual_transfer',
        'proof' => UploadedFile::fake()->image('proof.jpg'),
        'notes' => 'Paid via BCA',
    ])->assertRedirect(route('settings.billing'));

    $submission = BillingSubmission::first();
    expect($submission->company_id)->toBe($user->company_id);
    expect($submission->status)->toBe('pending');
    Storage::disk('public')->assertExists($submission->proof_path);
});

it('opens public invoices without login', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $invoice = Invoice::factory()->for($user->company)->for($client)->create(['number' => 'INV-PUBLIC']);

    $this->get(route('public.invoices.show', $invoice->public_token))
        ->assertOk()
        ->assertSee('INV-PUBLIC')
        ->assertSee($client->name);
});

it('enforces company scope and super admin permissions', function () {
    $owner = paperworkUser('owner');
    $otherOwner = paperworkUser('owner');
    $superAdmin = User::factory()->create(['company_id' => null, 'role' => 'super_admin']);
    $client = Client::factory()->for($owner->company)->create();
    $invoice = Invoice::factory()->for($owner->company)->for($client)->create();

    $this->actingAs($otherOwner)->get(route('invoices.show', $invoice))->assertForbidden();
    $this->actingAs($owner)->get(route('super-admin.index'))->assertForbidden();
    $this->actingAs($superAdmin)->get(route('super-admin.index'))->assertOk();
});

it('renders TailAdmin auth pages with Paperwork form actions', function () {
    $this->get(route('signin'))
        ->assertOk()
        ->assertSee('Masuk')
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('action="'.route('login.store').'"', false);

    $this->get(route('signup'))
        ->assertOk()
        ->assertSee('Daftar')
        ->assertSee('name="company_name"', false)
        ->assertSee('name="name"', false)
        ->assertSee('name="password_confirmation"', false)
        ->assertSee('action="'.route('register.store').'"', false);
});

it('renders create and edit forms inside modals on index pages', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $product = Product::factory()->for($user->company)->create();
    $invoice = Invoice::factory()->for($user->company)->for($client)->create();
    $quotation = Quotation::factory()->for($user->company)->for($client)->create();

    $this->actingAs($user)->get(route('clients.index', ['modal' => 'create']))
        ->assertOk()
        ->assertSee('open-modal', false)
        ->assertSee('Tambah Klien')
        ->assertSee('edit-client');

    $this->actingAs($user)->get(route('products.index', ['modal' => 'create']))
        ->assertOk()
        ->assertSee('Tambah Produk')
        ->assertSee('edit-product');

    $this->actingAs($user)->get(route('invoices.index', ['modal' => 'create', 'edit' => $invoice->id]))
        ->assertOk()
        ->assertSee('Buat Invoice')
        ->assertSee('edit-invoice-'.$invoice->id);

    $this->actingAs($user)->get(route('quotations.index', ['modal' => 'create', 'edit' => $quotation->id]))
        ->assertOk()
        ->assertSee('Buat Penawaran')
        ->assertSee('edit-quotation-'.$quotation->id);
});

it('renders client stats and searchable datatable', function () {
    $user = paperworkUser();
    $visibleClient = Client::factory()->for($user->company)->create([
        'name' => 'Nusa Legal',
        'company_name' => 'Nusa Legal Indonesia',
        'email' => 'finance@nusa.test',
    ]);
    Client::factory()->for($user->company)->create(['name' => 'Aruna Studio']);
    Invoice::factory()->for($user->company)->for($visibleClient)->create(['total' => 250000]);
    Quotation::factory()->for($user->company)->for($visibleClient)->create();

    $this->actingAs($user)->get(route('clients.index'))
        ->assertOk()
        ->assertSee('Total Klien')
        ->assertSee('Klien Aktif')
        ->assertSee('Nilai Invoice')
        ->assertSee('Klien Baru')
        ->assertSee('Data Klien');

    $response = $this->actingAs($user)->get(route('clients.index', ['datatable' => 1]))
        ->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(2);
    expect($data[0]['client'])->toContain('Nusa Legal');
    expect($data[0]['documents'])->toContain('1 invoice');
    expect($data[0]['documents'])->toContain('1 penawaran');
    expect($data[0]['invoice_value'])->toContain('Rp 250.000');

    $responseSearch = $this->actingAs($user)->get(route('clients.index', ['datatable' => 1, 'search' => ['value' => 'finance@nusa.test']]))
        ->assertOk();
    $dataSearch = $responseSearch->json('data');
    expect($dataSearch)->toHaveCount(1);
    expect($dataSearch[0]['client'])->toContain('Nusa Legal');
    expect($dataSearch[0]['client'])->not->toContain('Aruna Studio');
});

it('renders billing pricing cards and creates a Pakasir payment detail page', function () {
    Http::fake([
        'https://app.pakasir.com/api/transactioncreate/qris' => Http::response([
            'payment_number' => 'QRIS-ORDER-001',
            'payment_url' => 'https://app.pakasir.com/pay/QRIS-ORDER-001',
        ]),
    ]);

    config([
        'services.pakasir.base_url' => 'https://app.pakasir.com/api',
        'services.pakasir.project' => 'paperwork',
        'services.pakasir.api_key' => 'test-key',
    ]);

    $user = paperworkUser();

    $this->actingAs($user)->get(route('settings.billing'))
        ->assertOk()
        ->assertSee('Starter')
        ->assertSee('Business')
        ->assertSee('Enterprise')
        ->assertSee('confirm-payment-starter');

    $this->actingAs($user)->post(route('billing.store'), [
        'package' => 'business',
        'billing_period' => 'monthly',
        'amount' => 149000,
        'payment_method' => 'qris',
    ])->assertRedirect();

    $submission = BillingSubmission::first();
    expect($submission->payment_gateway)->toBe('pakasir');
    expect($submission->payment_order_id)->toBe('PAPERWORK-B'.str_pad((string) $submission->id, 5, '0', STR_PAD_LEFT));

    $this->actingAs($user)->get(route('settings.billing.show', $submission))
        ->assertOk()
        ->assertSee('Business')
        ->assertSee('QRIS-ORDER-001')
        ->assertSee('https://app.pakasir.com/pay/QRIS-ORDER-001');
});

it('keeps company profile focused on company identity fields', function () {
    $user = paperworkUser();

    $this->actingAs($user)->get(route('settings.company'))
        ->assertOk()
        ->assertSee('Logo Perusahaan')
        ->assertSee('Nama PIC')
        ->assertDontSee('Bank Name')
        ->assertDontSee('Bank Account Number');

    Storage::fake('public');

    $this->actingAs($user)->put(route('settings.company.update'), [
        'logo' => UploadedFile::fake()->image('logo.png'),
        'name' => 'Paperwork Studio',
        'email' => 'hello@paperwork.test',
        'phone' => '08123456789',
        'address' => 'Jl. Dokumen No. 1',
        'pic_name' => 'Nadia',
        'pic_email' => 'nadia@paperwork.test',
        'pic_phone' => '08987654321',
        'tax_number' => 'NPWP-001',
    ])->assertRedirect(route('settings.company'));

    $user->company->refresh();
    expect($user->company->name)->toBe('Paperwork Studio');
    expect($user->company->pic_name)->toBe('Nadia');
    Storage::disk('public')->assertExists($user->company->logo_path);
});

it('lists company bank accounts in a filterable table', function () {
    $user = paperworkUser();
    BankAccount::factory()->for($user->company)->create([
        'bank_name' => 'BCA',
        'account_name' => 'Paperwork Operating',
        'account_number' => '1234567890',
        'branch' => 'Jakarta',
        'currency' => 'IDR',
        'is_active' => true,
    ]);
    BankAccount::factory()->for($user->company)->create([
        'bank_name' => 'Mandiri',
        'account_name' => 'Paperwork Reserve',
        'account_number' => '999000111',
        'currency' => 'USD',
        'is_active' => false,
    ]);

    $this->actingAs($user)->get(route('settings.bank-accounts', ['bank' => 'BCA', 'status' => 'active']))
        ->assertOk()
        ->assertSee('Bank')
        ->assertSee('Nama Rekening')
        ->assertSee('Nomor Rekening')
        ->assertSee('Mata Uang')
        ->assertSee('Status')
        ->assertSee('BCA')
        ->assertDontSee('Paperwork Reserve');
});

it('renders dashboard invoice stats and interactive chart datasets', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $oldInvoice = Invoice::factory()->for($user->company)->for($client)->create([
        'number' => 'INV-DASH-OLD',
        'issue_date' => now()->subMonth()->startOfMonth()->toDateString(),
        'due_date' => now()->subMonth()->addDays(7)->toDateString(),
        'status' => 'paid',
        'total' => 300000,
    ]);
    $currentInvoice = Invoice::factory()->for($user->company)->for($client)->create([
        'number' => 'INV-DASH-CURRENT',
        'issue_date' => now()->toDateString(),
        'due_date' => now()->subDay()->toDateString(),
        'status' => 'partial',
        'total' => 500000,
    ]);
    $oldInvoice->payments()->create([
        'amount' => 300000,
        'paid_at' => now()->subMonth()->startOfMonth()->addDays(2)->toDateString(),
        'method' => 'bank_transfer',
    ]);
    $currentInvoice->payments()->create([
        'amount' => 200000,
        'paid_at' => now()->toDateString(),
        'method' => 'bank_transfer',
    ]);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('dashboard-stats-grid', false)
        ->assertSee('Total Invoice')
        ->assertSee('Nilai Diterbitkan')
        ->assertSee('Pendapatan Tertagih')
        ->assertSee('Piutang')
        ->assertSee('Jatuh Tempo')
        ->assertSee('invoiceRevenueLineChart', false)
        ->assertSee('invoiceRevenueLineCanvas', false)
        ->assertSee('invoiceStatusBarChart', false)
        ->assertSee('invoiceStatusBarCanvas', false)
        ->assertSee('dashboardChartData', false)
        ->assertSee('new Chart', false)
        ->assertSee('500000', false)
        ->assertSee('300000', false)
        ->assertSee('partial', false)
        ->assertSee('paid', false);
});

it('automatically converts to invoice and redirects when quotation status is updated to approved', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $quotation = Quotation::factory()->for($user->company)->for($client)->create([
        'number' => 'QUO-AUTO-001',
        'status' => 'draft',
        'subtotal' => 150000,
        'tax_rate' => 0,
        'tax_total' => 0,
        'total' => 150000,
    ]);
    $quotation->items()->create([
        'description' => 'Service test',
        'quantity' => 1,
        'unit_price' => 150000,
        'line_total' => 150000,
    ]);

    $response = $this->actingAs($user)
        ->patch(route('quotations.status', $quotation), ['status' => 'approved']);

    $invoice = Invoice::where('quotation_id', $quotation->id)->first();
    expect($invoice)->not->toBeNull();
    $response->assertRedirect(route('invoices.show', $invoice));

    expect($quotation->refresh()->status)->toBe('converted');
});

it('hides approved and converted quotations from the index table', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    
    $draftQuotation = Quotation::factory()->for($user->company)->for($client)->create([
        'number' => 'QUO-DRAFT-999',
        'status' => 'draft',
    ]);
    
    $approvedQuotation = Quotation::factory()->for($user->company)->for($client)->create([
        'number' => 'QUO-APPROVED-999',
        'status' => 'approved',
    ]);

    $convertedQuotation = Quotation::factory()->for($user->company)->for($client)->create([
        'number' => 'QUO-CONVERTED-999',
        'status' => 'converted',
    ]);

    $this->actingAs($user)->get(route('quotations.index', ['datatable' => 1]))
        ->assertOk()
        ->assertSee('QUO-DRAFT-999')
        ->assertDontSee('QUO-APPROVED-999')
        ->assertDontSee('QUO-CONVERTED-999');
});

it('renders the privacy policy and terms of service pages and links on login/register pages', function () {
    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Kebijakan Privasi Paperwork')
        ->assertSee('Informasi Yang Kami Kumpulkan');

    $this->get(route('terms-of-service'))
        ->assertOk()
        ->assertSee('Ketentuan Pelanggan Paperwork')
        ->assertSee('Penerimaan Ketentuan');

    $this->get(route('signin'))
        ->assertOk()
        ->assertSee(route('privacy-policy'))
        ->assertSee(route('terms-of-service'));

    $this->get(route('signup'))
        ->assertOk()
        ->assertSee(route('privacy-policy'))
        ->assertSee(route('terms-of-service'));
});

it('formats quotation item descriptions with smaller font and handles hyphens as newlines', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();
    $quotation = Quotation::factory()->for($user->company)->for($client)->create([
        'number' => 'QUO-DESCR-123',
    ]);
    $quotation->items()->create([
        'description' => 'Item Utama - Detail A - Detail B',
        'quantity' => 1,
        'unit_price' => 100000,
        'line_total' => 100000,
    ]);

    $this->actingAs($user)->get(route('quotations.show', $quotation))
        ->assertOk()
        ->assertSee('text-xs', false)
        ->assertSee('Item Utama<br>Detail A<br>Detail B', false);
});

it('customizes invoice numbering template and auto-generates sequential invoice numbers', function () {
    $user = paperworkUser();
    $user->company->update([
        'invoice_number_prefix' => 'FAKTUR',
        'invoice_number_format' => '{PREFIX}/{ROMAN}/{YYYY}/{NUMBER}',
        'invoice_number_padding' => 4,
        'invoice_next_number' => 10,
    ]);

    $generated = $user->company->generateNextInvoiceNumber(now(), true);
    $monthRoman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][now()->month - 1];
    $expected = 'FAKTUR/' . $monthRoman . '/' . now()->year . '/0010';
    expect($generated)->toBe($expected);
    expect($user->company->refresh()->invoice_next_number)->toBe(11);
});

it('processes Pakasir POST webhook JSON payload and activates user subscription automatically', function () {
    config(['services.pakasir.project' => 'depodomain']);

    $user = paperworkUser();
    $submission = BillingSubmission::create([
        'company_id' => $user->company_id,
        'package' => 'business',
        'billing_period' => 'monthly',
        'amount' => 22000,
        'payment_method' => 'qris',
        'payment_gateway' => 'pakasir',
        'payment_order_id' => 'PAPERWORK-B00099',
        'status' => 'pending',
    ]);

    $payload = [
        'amount' => 22000,
        'order_id' => 'PAPERWORK-B00099',
        'project' => 'depodomain',
        'status' => 'completed',
        'payment_method' => 'qris',
        'completed_at' => '2024-09-10T08:07:02.819+07:00',
    ];

    $response = $this->postJson(route('webhooks.pakasir'), $payload);
    $response->assertOk()
        ->assertJson(['message' => 'Billing berhasil diaktifkan.']);

    expect($submission->refresh()->status)->toBe('confirmed');
    expect($user->company->refresh()->active_plan)->toBe('business');
});

it('allows superadmin to configure payment gateway to sumopod and processes sumopod webhook', function () {
    $superadmin = paperworkUser('super_admin');

    // 1. Test Superadmin settings view & update
    $this->actingAs($superadmin)->get(route('super-admin.settings'))
        ->assertOk()
        ->assertSee('Pengaturan Payment Gateway Superadmin');

    $this->actingAs($superadmin)->put(route('super-admin.settings.update'), [
        'active_payment_gateway' => 'sumopod',
        'sumopod_base_url' => 'https://api-pay-sandbox.sumopod.com/api/v1',
        'sumopod_api_key' => 'test_sumopod_key_123',
    ])->assertRedirect();

    expect(\App\Models\SystemSetting::get('active_payment_gateway'))->toBe('sumopod');
    expect(\App\Models\SystemSetting::get('sumopod_api_key'))->toBe('test_sumopod_key_123');

    // 2. Test Sumopod Webhook execution
    $user = paperworkUser();
    $submission = BillingSubmission::create([
        'company_id' => $user->company_id,
        'package' => 'enterprise',
        'billing_period' => 'yearly',
        'amount' => 299000,
        'payment_method' => 'qris',
        'payment_gateway' => 'sumopod',
        'payment_order_id' => 'PAPERWORK-SUMO-001',
        'status' => 'pending',
    ]);

    $payload = [
        'payment_id' => 'uuid-12345',
        'order_id' => 'PAPERWORK-SUMO-001',
        'amount' => 299000,
        'status' => 'paid',
        'payment_code' => '1308300301295957',
        'payment_link_url' => 'https://pay.sumopod.com/pay/uuid-12345',
    ];

    $response = $this->postJson(route('webhooks.sumopod'), $payload);
    $response->assertOk()
        ->assertJson(['message' => 'Billing berhasil diaktifkan via Sumopod.']);

    expect($submission->refresh()->status)->toBe('confirmed');
    expect($user->company->refresh()->active_plan)->toBe('enterprise');

    // 3. Test rendering billing show page with payment_link_url converted to QR Code
    $submissionOnlyUrl = BillingSubmission::create([
        'company_id' => $user->company_id,
        'package' => 'business',
        'billing_period' => 'monthly',
        'amount' => 149000,
        'payment_method' => 'qris',
        'payment_gateway' => 'sumopod',
        'payment_order_id' => 'PAPERWORK-SUMO-002',
        'payment_url' => 'https://pay.sumopod.com/pay/uuid-67890',
        'payment_payload' => [
            'payment_id' => 'uuid-67890',
            'payment_link_url' => 'https://pay.sumopod.com/pay/uuid-67890',
            'status' => 'pending',
        ],
        'status' => 'pending',
    ]);

    $this->actingAs($user)->get(route('settings.billing.show', $submissionOnlyUrl))
        ->assertOk()
        ->assertSee('https://pay.sumopod.com/pay/uuid-67890')
        ->assertSee('data:image/png;base64,');
});

it('supports custom taxes with addition and deduction types for invoices and quotations', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();

    // 1. Create Invoice with custom taxes: PPN 11% (addition) & PPh 23 2% (deduction) & Service Charge 5% (addition)
    $response = $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'number' => 'INV-TAX-CUSTOM-01',
        'issue_date' => now()->toDateString(),
        'items' => [
            ['description' => 'Jasa Pembuatan Software', 'quantity' => 1, 'unit_price' => 1000000],
        ],
        'custom_taxes' => [
            ['name' => 'PPN', 'rate' => 11, 'type' => 'addition'],
            ['name' => 'Service Charge', 'rate' => 5, 'type' => 'addition'],
            ['name' => 'PPh 23', 'rate' => 2, 'type' => 'deduction'],
        ],
    ]);

    $invoice = Invoice::where('number', 'INV-TAX-CUSTOM-01')->first();
    expect($invoice)->not->toBeNull();
    $response->assertRedirect(route('invoices.show', $invoice));

    // Subtotal = 1.000.000
    // PPN (11%) = 110.000 (+)
    // Service Charge (5%) = 50.000 (+)
    // PPh 23 (2%) = 20.000 (-)
    // Total = 1.000.000 + 110.000 + 50.000 - 20.000 = 1.140.000
    expect((float) $invoice->subtotal)->toBe(1000000.0);
    expect((float) $invoice->total)->toBe(1140000.0);
    expect(count($invoice->normalized_custom_taxes))->toBe(3);

    $this->actingAs($user)->get(route('invoices.show', $invoice))
        ->assertOk()
        ->assertSee('PPN (11%)')
        ->assertSee('Service Charge (5%)')
        ->assertSee('PPh 23 (2%)');

    // 2. Create Quotation with custom taxes
    $quoResponse = $this->actingAs($user)->post(route('quotations.store'), [
        'client_id' => $client->id,
        'number' => 'QUO-TAX-CUSTOM-01',
        'issue_date' => now()->toDateString(),
        'items' => [
            ['description' => 'Konsultasi IT', 'quantity' => 2, 'unit_price' => 500000],
        ],
        'custom_taxes' => [
            ['name' => 'Pajak Daerah', 'rate' => 10, 'type' => 'addition'],
        ],
    ]);

    $quotation = Quotation::where('number', 'QUO-TAX-CUSTOM-01')->first();
    expect($quotation)->not->toBeNull();
    $quoResponse->assertRedirect(route('quotations.show', $quotation));
    expect((float) $quotation->subtotal)->toBe(1000000.0);
    expect((float) $quotation->total)->toBe(1100000.0);
});

it('supports split payment terms when creating quotation and copies terms when converted to invoice', function () {
    $user = paperworkUser();
    $user->company->update(['active_plan' => 'enterprise', 'subscription_ends_at' => now()->addYear()]);
    $client = Client::factory()->for($user->company)->create();

    // 1. Create Quotation with split payment terms (DP 50% & Pelunasan 50%)
    $response = $this->actingAs($user)->post(route('quotations.store'), [
        'client_id' => $client->id,
        'number' => 'QUO-SPLIT-01',
        'issue_date' => now()->toDateString(),
        'items' => [
            ['description' => 'Desain Landing Page', 'quantity' => 1, 'unit_price' => 2000000],
        ],
        'payment_terms' => [
            ['label' => 'DP 50%', 'amount' => 1000000, 'due_date' => now()->toDateString()],
            ['label' => 'Pelunasan 50%', 'amount' => 1000000, 'due_date' => now()->addDays(14)->toDateString()],
        ],
    ]);

    $quotation = Quotation::where('number', 'QUO-SPLIT-01')->first();
    expect($quotation)->not->toBeNull();
    expect($quotation->paymentTerms)->toHaveCount(2);

    $this->actingAs($user)->get(route('quotations.show', $quotation))
        ->assertOk()
        ->assertSee('Termin Pembayaran')
        ->assertSee('DP 50%')
        ->assertSee('Pelunasan 50%');

    // 2. Approve and convert quotation to invoice
    $this->actingAs($user)->patch(route('quotations.status', $quotation), ['status' => 'approved']);

    $invoice = Invoice::where('quotation_id', $quotation->id)->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->paymentTerms)->toHaveCount(2);
    expect((float) $invoice->paymentTerms->first()->amount)->toBe(1000000.0);
});

it('supports optional discount when creating invoices and quotations', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();

    // 1. Invoice with 10% discount
    // Subtotal = 1.000.000, Diskon 10% = 100.000, Discounted Subtotal = 900.000
    // PPN 11% = 99.000, Total = 999.000
    $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'number' => 'INV-DISC-10',
        'issue_date' => now()->toDateString(),
        'discount_type' => 'percentage',
        'discount_rate' => 10,
        'tax_rate' => 11,
        'items' => [
            ['description' => 'Paket Jasa A', 'quantity' => 1, 'unit_price' => 1000000],
        ],
    ]);

    $invoice = Invoice::where('number', 'INV-DISC-10')->first();
    expect($invoice)->not->toBeNull();
    expect((float) $invoice->subtotal)->toBe(1000000.0);
    expect((float) $invoice->discount_amount)->toBe(100000.0);
    expect((float) $invoice->tax_total)->toBe(99000.0);
    expect((float) $invoice->total)->toBe(999000.0);

    $this->actingAs($user)->get(route('invoices.show', $invoice))
        ->assertOk()
        ->assertSee('Diskon (10%)');

    // 2. Quotation with fixed discount Rp 50.000
    // Subtotal = 500.000, Diskon = 50.000, Discounted Subtotal = 450.000, Total = 450.000
    $this->actingAs($user)->post(route('quotations.store'), [
        'client_id' => $client->id,
        'number' => 'QUO-DISC-FIXED',
        'issue_date' => now()->toDateString(),
        'discount_type' => 'fixed',
        'discount_amount' => 50000,
        'custom_taxes' => [],
        'items' => [
            ['description' => 'Desain Logo', 'quantity' => 1, 'unit_price' => 500000],
        ],
    ]);

    $quotation = Quotation::where('number', 'QUO-DISC-FIXED')->first();
    expect($quotation)->not->toBeNull();
    expect((float) $quotation->discount_amount)->toBe(50000.0);
    expect((float) $quotation->total)->toBe(450000.0);
});

it('supports exporting and importing JSON backup data', function () {
    $user = paperworkUser();

    // 1. Export JSON data
    $response = $this->actingAs($user)->get(route('settings.data.export'));
    $response->assertOk();

    // 2. Import JSON backup file
    $jsonPath = '/Users/user/Downloads/paperwork-account-4 (1).json';
    if (file_exists($jsonPath)) {
        $file = new \Illuminate\Http\UploadedFile(
            $jsonPath,
            'backup.json',
            'application/json',
            null,
            true
        );

        $importResponse = $this->actingAs($user)->post(route('settings.data.import'), [
            'json_file' => $file,
        ]);

        $importResponse->assertRedirect()->assertSessionHas('success');
    }
});

it('supports creating down payment invoice and settlement invoice with parent reference', function () {
    $user = paperworkUser();
    $client = Client::factory()->for($user->company)->create();

    // 1. Create Down Payment (DP) Invoice for Rp 1.000.000
    $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'number' => 'INV-DP-001',
        'issue_date' => now()->toDateString(),
        'invoice_type' => 'down_payment',
        'items' => [
            ['description' => 'DP Pembuatan Website 50%', 'quantity' => 1, 'unit_price' => 1000000],
        ],
    ]);

    $dpInvoice = Invoice::where('number', 'INV-DP-001')->first();
    expect($dpInvoice)->not->toBeNull();
    expect($dpInvoice->invoice_type)->toBe('down_payment');
    expect((float) $dpInvoice->total)->toBe(1000000.0);

    $this->actingAs($user)->get(route('invoices.show', $dpInvoice))
        ->assertOk()
        ->assertSee('INVOICE UANG MUKA (DP)')
        ->assertSee('Buat Invoice Pelunasan');

    // 2. Create Settlement Invoice referencing the DP Invoice
    $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'number' => 'INV-SETTLE-001',
        'issue_date' => now()->toDateString(),
        'invoice_type' => 'settlement',
        'parent_invoice_id' => $dpInvoice->id,
        'items' => [
            ['description' => 'Pelunasan Pembuatan Website 50%', 'quantity' => 1, 'unit_price' => 1000000],
        ],
    ]);

    $settleInvoice = Invoice::where('number', 'INV-SETTLE-001')->first();
    expect($settleInvoice)->not->toBeNull();
    expect($settleInvoice->invoice_type)->toBe('settlement');
    expect($settleInvoice->parent_invoice_id)->toBe($dpInvoice->id);

    $this->actingAs($user)->get(route('invoices.show', $settleInvoice))
        ->assertOk()
        ->assertSee('INVOICE PELUNASAN')
        ->assertSee('Referensi Invoice Uang Muka (DP):')
        ->assertSee('INV-DP-001');
});

it('supports expense management web CRUD operations', function () {
    $user = paperworkUser();

    // 1. Store expense
    $response = $this->actingAs($user)->post(route('expenses.store'), [
        'category' => 'Operasional',
        'amount' => 150000,
        'date' => now()->toDateString(),
        'description' => 'Beli Kertas HVS & Tinta Printer',
    ]);
    $response->assertRedirect(route('expenses.index'));

    $expense = \App\Models\Expense::where('category', 'Operasional')->first();
    expect($expense)->not->toBeNull();
    expect((float) $expense->amount)->toBe(150000.0);

    // 2. View datatable list
    $this->actingAs($user)->get(route('expenses.index', ['datatable' => 1]))
        ->assertOk()
        ->assertJsonFragment(['category' => '<span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 dark:bg-white/10 dark:text-gray-200">Operasional</span>']);

    // 3. Update expense
    $this->actingAs($user)->put(route('expenses.update', $expense), [
        'category' => 'Sewa & Utilitas',
        'amount' => 200000,
        'date' => now()->toDateString(),
        'description' => 'Bayar Tagihan Listrik',
    ])->assertRedirect(route('expenses.index'));

    expect($expense->refresh()->category)->toBe('Sewa & Utilitas');
    expect((float) $expense->amount)->toBe(200000.0);

    // 4. Delete expense
    $this->actingAs($user)->delete(route('expenses.destroy', $expense))
        ->assertRedirect(route('expenses.index'));
    expect(\App\Models\Expense::find($expense->id))->toBeNull();
});

it('renders financial reports and exports csv and pdf', function () {
    $user = paperworkUser();

    // 1. Render Reports page for each tab
    foreach (['cash-flow', 'profit-loss', 'aging-ar', 'tax-summary'] as $tab) {
        $this->actingAs($user)->get(route('reports.index', ['tab' => $tab]))
            ->assertOk();
    }

    // 2. Export CSV
    $csvResponse = $this->actingAs($user)->get(route('reports.export', ['type' => 'tax-summary']));
    $csvResponse->assertOk();
    expect($csvResponse->headers->get('content-type'))->toContain('text/csv');

    // 3. Export PDF
    $pdfResponse = $this->actingAs($user)->get(route('reports.pdf', ['type' => 'profit-loss']));
    $pdfResponse->assertOk();
    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');
});

it('renders security settings page and updates user password', function () {
    $user = paperworkUser();
    $user->update(['password' => \Illuminate\Support\Facades\Hash::make('oldpassword123')]);

    // 1. Render security page
    $this->actingAs($user)->get(route('settings.security'))
        ->assertOk()
        ->assertSee('Ubah Kata Sandi')
        ->assertSee('Akses Perangkat & Token Aplikasi', false);

    // 2. Change password with incorrect current password
    $this->actingAs($user)->put(route('settings.security.password'), [
        'current_password' => 'wrongpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertSessionHasErrors('current_password');

    // 3. Change password with correct current password
    $this->actingAs($user)->put(route('settings.security.password'), [
        'current_password' => 'oldpassword123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertRedirect(route('settings.security'))
      ->assertSessionHas('success');

    expect(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->refresh()->password))->toBeTrue();
});

it('auto generates invoice and quotation numbers when number field is left empty', function () {
    $user = paperworkUser();
    $client = Client::factory()->create(['company_id' => $user->company_id]);

    // 1. Store invoice with empty number
    $invResponse = $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'number' => '', // Left empty
        'issue_date' => now()->toDateString(),
        'items' => [
            ['description' => 'Produk Auto Numbering', 'quantity' => 1, 'unit_price' => 150000],
        ],
    ]);

    $invoice = Invoice::where('company_id', $user->company_id)->latest('id')->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->number)->not->toBeEmpty();
    $invResponse->assertRedirect(route('invoices.show', $invoice));

    // 2. Store quotation with empty number
    $quoResponse = $this->actingAs($user)->post(route('quotations.store'), [
        'client_id' => $client->id,
        'number' => '', // Left empty
        'issue_date' => now()->toDateString(),
        'items' => [
            ['description' => 'Penawaran Auto Numbering', 'quantity' => 1, 'unit_price' => 200000],
        ],
    ]);

    $quotation = Quotation::where('company_id', $user->company_id)->latest('id')->first();
    expect($quotation)->not->toBeNull();
    expect($quotation->number)->not->toBeEmpty();
    $quoResponse->assertRedirect(route('quotations.show', $quotation));
});

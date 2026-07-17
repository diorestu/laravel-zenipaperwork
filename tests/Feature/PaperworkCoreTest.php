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
        ->assertSee('Sign In')
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('action="'.route('login.store').'"', false);

    $this->get(route('signup'))
        ->assertOk()
        ->assertSee('Sign Up')
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
        ->assertSee('Tambah Client')
        ->assertSee('edit-client');

    $this->actingAs($user)->get(route('products.index', ['modal' => 'create']))
        ->assertOk()
        ->assertSee('Tambah Product')
        ->assertSee('edit-product');

    $this->actingAs($user)->get(route('invoices.index', ['modal' => 'create', 'edit' => $invoice->id]))
        ->assertOk()
        ->assertSee('Create Invoice')
        ->assertSee('edit-invoice-'.$invoice->id);

    $this->actingAs($user)->get(route('quotations.index', ['modal' => 'create', 'edit' => $quotation->id]))
        ->assertOk()
        ->assertSee('Create Quotation')
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
        ->assertSee('Total Client')
        ->assertSee('Client Aktif')
        ->assertSee('Nilai Invoice')
        ->assertSee('Client Baru')
        ->assertSee('Data Client');

    $response = $this->actingAs($user)->get(route('clients.index', ['datatable' => 1]))
        ->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(2);
    expect($data[0]['client'])->toContain('Nusa Legal');
    expect($data[0]['documents'])->toContain('1 invoice');
    expect($data[0]['documents'])->toContain('1 quotation');
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
        'amount' => 149000,
        'payment_method' => 'qris',
    ])->assertRedirect();

    $submission = BillingSubmission::first();
    expect($submission->payment_gateway)->toBe('pakasir');
    expect($submission->payment_order_id)->toBe('BILL-'.$submission->id);

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
        ->assertSee('Company Logo')
        ->assertSee('PIC Name')
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
        ->assertSee('Account Name')
        ->assertSee('Account Number')
        ->assertSee('Currency')
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
        ->assertSee('Total Invoices')
        ->assertSee('Issued Amount')
        ->assertSee('Collected Revenue')
        ->assertSee('Outstanding')
        ->assertSee('Overdue')
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

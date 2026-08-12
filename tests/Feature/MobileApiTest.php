<?php

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function apiUser(): User
{
    $company = Company::factory()->create(['trial_ends_at' => now()->addDays(30)]);

    return User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);
}

it('validates google login token requirement via API', function () {
    $this->postJson(route('api.auth.google'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['id_token']);
});

it('returns mobile dashboard metrics and recent invoices via API', function () {
    $user = apiUser();
    $client = Client::factory()->for($user->company)->create();
    Invoice::factory()->for($user->company)->for($client)->create(['total' => 500000]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson(route('api.dashboard'))
        ->assertOk()
        ->assertJsonStructure([
            'stats' => ['total_invoices', 'issued_amount', 'collected_revenue', 'outstanding_balance', 'overdue_count'],
            'counts' => ['invoices', 'quotations', 'clients', 'products'],
            'chart_data' => ['months', 'issued', 'collected', 'status_labels', 'status_counts'],
            'recent_invoices',
        ]);

    expect($response->json('counts.invoices'))->toBe(1);
});

it('records invoice payment with proof file upload via API', function () {
    $user = apiUser();
    $client = Client::factory()->for($user->company)->create();
    $invoice = Invoice::factory()->for($user->company)->for($client)->create(['total' => 300000]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('api.invoices.payments.store', $invoice), [
            'amount' => 150000,
            'paid_at' => '2026-08-05',
            'method' => 'bank_transfer',
            'reference' => 'MOB-PAY-001',
        ])
        ->assertOk();

    expect($response->json('message'))->toBe('Pembayaran berhasil dicatat.');
    expect($invoice->refresh()->status)->toBe('partial');
    expect($invoice->amount_paid)->toBe('150000.00');
    expect($invoice->balance_due)->toBe('150000.00');
});

it('converts approved quotation to invoice via API', function () {
    $user = apiUser();
    $client = Client::factory()->for($user->company)->create();
    $quotation = Quotation::factory()->for($user->company)->for($client)->create([
        'status' => 'approved',
        'subtotal' => 100000,
        'total' => 100000,
    ]);
    $quotation->items()->create(['description' => 'Service A', 'quantity' => 1, 'unit_price' => 100000, 'line_total' => 100000]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('api.quotations.convert', $quotation), [
            'number' => 'INV-API-CONV-001',
        ])
        ->assertOk();

    expect($response->json('message'))->toBe('Penawaran berhasil dikonversi ke invoice.');
    $invoice = Invoice::where('number', 'INV-API-CONV-001')->first();
    expect($invoice)->not->toBeNull();
});

it('manages bank accounts via API', function () {
    $user = apiUser();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('api.bank-accounts.store'), [
            'bank_name' => 'BCA',
            'account_name' => 'PT Paperwork Mobile',
            'account_number' => '888000111',
            'currency' => 'IDR',
            'is_primary' => true,
        ])
        ->assertCreated();

    expect($response->json('data.bank_name'))->toBe('BCA');

    $this->actingAs($user, 'sanctum')
        ->getJson(route('api.bank-accounts.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('fetches unread notifications via API', function () {
    $user = apiUser();
    \App\Models\AppNotification::create([
        'company_id' => $user->company_id,
        'title' => 'Invoice Overdue',
        'body' => 'INV-001 overdue',
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson(route('api.notifications.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->actingAs($user, 'sanctum')
        ->postJson(route('api.notifications.read-all'))
        ->assertOk();
});

it('registers and deletes mobile device push tokens via API', function () {
    $user = apiUser();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('api.device-tokens.store'), [
            'token' => 'fcm_sample_token_xyz_123',
            'device_type' => 'android',
            'device_name' => 'Pixel 8 Pro',
        ])
        ->assertOk();

    expect($response->json('message'))->toBe('Token perangkat berhasil didaftarkan.');
    expect(\App\Models\UserDeviceToken::where('user_id', $user->id)->count())->toBe(1);

    // Test sending notification via push service
    $pushService = new \App\Services\FirebasePushService();
    $sent = $pushService->sendToCompany($user->company_id, 'Tes Push', 'Pesan Notifikasi Tes');
    expect($sent)->toBeInt();

    // Delete token
    $this->actingAs($user, 'sanctum')
        ->deleteJson(route('api.device-tokens.destroy'), [
            'token' => 'fcm_sample_token_xyz_123',
        ])
        ->assertOk();

    expect(\App\Models\UserDeviceToken::where('user_id', $user->id)->count())->toBe(0);
});

it('renders mobile workspace UI page for authenticated users', function () {
    $user = apiUser();

    $this->actingAs($user)
        ->get(route('mobile.app'))
        ->assertOk()
        ->assertSee('Total Penjualan')
        ->assertSee('Buat Invoice Baru')
        ->assertSee('Daftar Klien');
});

it('automatically redirects mobile user agents to mobile workspace', function () {
    $user = apiUser();

    // Mobile user agent request
    $response = $this->actingAs($user)
        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1'])
        ->get(route('dashboard'));

    $response->assertRedirect(route('mobile.app'));

    // With ?desktop=1 override
    $overrideResponse = $this->actingAs($user)
        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1'])
        ->get(route('dashboard', ['desktop' => 1]));

    $overrideResponse->assertOk();
});

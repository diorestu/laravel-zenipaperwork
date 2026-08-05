<?php

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function testUser(): User
{
    $company = Company::factory()->create();

    return User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);
}

it('renders the calendar page with navigation and due invoices', function () {
    $user = testUser();
    $client = Client::factory()->for($user->company)->create();
    
    // Create an invoice due on today
    $invoice = Invoice::factory()->create([
        'company_id' => $user->company_id,
        'client_id' => $client->id,
        'number' => 'INV-CAL-001',
        'due_date' => now()->toDateString(),
        'total' => 1000000.00,
        'status' => 'sent',
    ]);

    $response = $this->actingAs($user)->get(route('calendar'));

    $response->assertOk();
    $response->assertSee('Kalender Jatuh Tempo');
    $response->assertSee('INV-CAL-001');
    $response->assertSee($client->name);
});

it('supports custom month and year navigation', function () {
    $user = testUser();

    $response = $this->actingAs($user)->get(route('calendar', [
        'month' => 12,
        'year' => 2026,
    ]));

    $response->assertOk();
    $response->assertSee('Desember 2026');
});

it('triggers manual sync and returns JSON response', function () {
    $user = testUser();

    $response = $this->actingAs($user)->post(route('calendar.sync'));

    $response->assertOk();
    $response->assertJson([
        'success' => true,
    ]);
});

it('automatically sets a 30-day free trial on company creation during registration', function () {
    $response = $this->post(route('register.store'), [
        'company_name' => 'Trial Tech Corp',
        'name' => 'John Trial',
        'email' => 'john@trial.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect(route('dashboard'));

    $company = Company::where('name', 'Trial Tech Corp')->first();
    expect($company)->not->toBeNull();
    expect($company->trial_ends_at)->not->toBeNull();
    
    $diffInDays = (int) round(now()->diffInDays($company->trial_ends_at));
    expect($diffInDays)->toBe(30);
});

it('automatically sets a 30-day free trial on company creation in middleware context fallback', function () {
    $user = User::factory()->create([
        'company_id' => null,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $user->refresh();
    expect($user->company_id)->not->toBeNull();
    
    $company = $user->company;
    expect($company->trial_ends_at)->not->toBeNull();
    
    $diffInDays = (int) round(now()->diffInDays($company->trial_ends_at));
    expect($diffInDays)->toBe(30);
});

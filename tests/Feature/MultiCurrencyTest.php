<?php

use App\Models\Account;
use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    // Seed default rates
    ExchangeRate::create(['currency' => 'USD', 'rate' => 15000.00]);
    ExchangeRate::create(['currency' => 'SGD', 'rate' => 11000.00]);
});

it('creates an account with USD currency and displays correct formatted balance', function () {
    $account = Account::create([
        'name' => 'USD Savings',
        'type' => 'bank',
        'balance' => 100.50,
        'currency' => 'USD',
    ]);

    expect($account->currency)->toBe('USD');
    expect($account->formatted_balance)->toBe('$ 100.50 (Rp 1.507.500)');
});

it('calculates the total balance on dashboard by converting USD to IDR', function () {
    // USD Account with $100 (equivalent to Rp 1,500,000)
    Account::create([
        'name' => 'USD Account',
        'type' => 'bank',
        'balance' => 100,
        'currency' => 'USD',
    ]);

    // IDR Account with Rp 500,000
    Account::create([
        'name' => 'IDR Account',
        'type' => 'bank',
        'balance' => 500000,
        'currency' => 'IDR',
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertSuccessful();

    // Total balance should be Rp 2,000,000
    $response->assertSee('2.000.000');
});

it('converts transfer amount correctly when transferring USD to IDR account', function () {
    $usdAccount = Account::create([
        'name' => 'USD Account',
        'type' => 'bank',
        'balance' => 100,
        'currency' => 'USD',
    ]);

    $idrAccount = Account::create([
        'name' => 'IDR Account',
        'type' => 'bank',
        'balance' => 100000,
        'currency' => 'IDR',
    ]);

    // Transfer $10 USD from USD Account to IDR Account
    // Expected debit on USD Account: $10 (leaves $90)
    // Expected credit on IDR Account: $10 * 15000 = Rp 150,000 (makes Rp 250,000)
    $response = $this->post(route('transactions.store'), [
        'account_id' => $usdAccount->id,
        'destination_account_id' => $idrAccount->id,
        'amount' => 10,
        'type' => 'transfer',
        'date' => now()->toDateString(),
        'description' => 'Transfer USD to IDR',
    ]);

    $response->assertRedirect();

    expect((float) $usdAccount->refresh()->balance)->toBe(90.00);
    expect((float) $idrAccount->refresh()->balance)->toBe(250000.00);
});

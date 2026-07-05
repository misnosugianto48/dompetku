<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the dashboard with regular and transfer transactions having null categories', function () {
    $account1 = Account::create([
        'name' => 'Account 1',
        'type' => 'bank',
        'balance' => 100000,
    ]);

    $account2 = Account::create([
        'name' => 'Account 2',
        'type' => 'bank',
        'balance' => 50000,
    ]);

    // Create a regular transaction with null category
    Transaction::create([
        'account_id' => $account1->id,
        'amount' => 1000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Uncategorized Expense',
    ]);

    // Create a transfer transaction
    Transaction::create([
        'account_id' => $account1->id,
        'destination_account_id' => $account2->id,
        'amount' => 5000,
        'type' => 'transfer',
        'date' => now()->toDateString(),
        'description' => 'Transfer between accounts',
    ]);

    $this->actingAs(User::factory()->create());

    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Transfer');
    $response->assertSee('Uncategorized');
});

<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('stores an expense transaction without an asset and updates account balance', function () {
    $account = Account::create([
        'name' => 'Main Account',
        'type' => 'bank',
        'balance' => 1000000,
    ]);

    $category = Category::create([
        'name' => 'Food',
        'type' => 'expense',
        'color' => '#000000',
    ]);

    $response = $this->withoutMiddleware()->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Lunch',
    ]);

    $response->assertRedirect(route('transactions.index'));

    expect((float) $account->refresh()->balance)->toBe(750000.0);

    $this->assertDatabaseHas('transactions', [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'description' => 'Lunch',
    ]);

    expect(Transaction::count())->toBe(1);
});

it('stores an income transaction without an asset and updates account balance', function () {
    $account = Account::create([
        'name' => 'Main Account',
        'type' => 'bank',
        'balance' => 1000000,
    ]);

    $category = Category::create([
        'name' => 'Salary',
        'type' => 'income',
        'color' => '#000000',
    ]);

    $response = $this->withoutMiddleware()->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 500000,
        'type' => 'income',
        'date' => now()->toDateString(),
        'description' => 'Salary',
    ]);

    $response->assertRedirect(route('transactions.index'));

    expect((float) $account->refresh()->balance)->toBe(1500000.0);
});

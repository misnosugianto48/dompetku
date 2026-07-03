<?php

use App\Models\Account;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('example', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
});

it('updates asset quantity and price when a linked transaction is created', function () {
    $user = User::factory()->create();
    $account = Account::create([
        'name' => 'Main Account',
        'type' => 'bank',
        'balance' => 10000000,
    ]);
    $category = Category::create([
        'name' => 'Investasi',
        'type' => 'expense',
        'color' => '#000000',
    ]);
    $asset = Asset::create([
        'user_id' => $user->id,
        'type' => 'stock',
        'name' => 'BBCA',
        'quantity' => 100,
        'purchase_price' => 8000,
        'current_price' => 9000,
        'platform' => 'Nanovest',
    ]);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'asset_id' => $asset->id,
        'amount' => 1000000, // Buy for 1,000,000
        'quantity' => 100,      // Buy 100 more
        'type' => 'expense',
        'date' => now()->format('Y-m-d'),
        'description' => 'Buying more BBCA',
    ]);

    $response->assertRedirect(route('transactions.index'));

    // Check account balance update (10,000,000 - 1,000,000 = 9,000,000)
    expect((float) $account->refresh()->balance)->toBe(9000000.0);

    // Check asset quantity (100 + 100 = 200)
    $asset->refresh();
    expect((float) $asset->quantity)->toBe(200.0);

    // Check average purchase price:
    // Old: 100 * 8000 = 800,000
    // New: 800,000 + 1,000,000 = 1,800,000
    // Avg: 1,800,000 / 200 = 9,000
    expect((float) $asset->purchase_price)->toBe(9000.0);
    expect((float) $asset->current_price)->toBe(10000.0); // 1,000,000 / 100

    // Check price history
    $this->assertDatabaseHas('asset_price_histories', [
        'asset_id' => $asset->id,
        'price' => 10000,
    ]);
});

it('decreases asset quantity when selling via transaction', function () {
    $user = User::factory()->create();
    $account = Account::create([
        'name' => 'Main Account',
        'type' => 'bank',
        'balance' => 1000000,
    ]);
    $category = Category::create([
        'name' => 'Investasi',
        'type' => 'income',
        'color' => '#000000',
    ]);
    $asset = Asset::create([
        'user_id' => $user->id,
        'type' => 'stock',
        'name' => 'BBCA',
        'quantity' => 100,
        'purchase_price' => 8000,
        'current_price' => 9000,
        'platform' => 'Nanovest',
    ]);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'asset_id' => $asset->id,
        'amount' => 500000, // Sell for 500,000
        'quantity' => 50,    // Sell 50
        'type' => 'income',
        'date' => now()->format('Y-m-d'),
        'description' => 'Selling some BBCA',
    ]);

    $asset->refresh();
    expect((float) $asset->quantity)->toBe(50.0);
    expect((float) $account->refresh()->balance)->toBe(1500000.0);
});

it('reverts asset quantity and price when a linked transaction is deleted', function () {
    $user = User::factory()->create();
    $account = Account::create([
        'name' => 'Main Account',
        'type' => 'bank',
        'balance' => 10000000,
    ]);
    $category = Category::create([
        'name' => 'Investasi',
        'type' => 'expense',
        'color' => '#000000',
    ]);
    $asset = Asset::create([
        'user_id' => $user->id,
        'type' => 'stock',
        'name' => 'BBCA',
        'quantity' => 100,
        'purchase_price' => 8000,
        'current_price' => 9000,
        'platform' => 'Nanovest',
    ]);

    // Create a transaction via post
    $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'asset_id' => $asset->id,
        'amount' => 1000000, // Buy for 1,000,000
        'quantity' => 100,      // Buy 100 more
        'type' => 'expense',
        'date' => now()->format('Y-m-d'),
        'description' => 'Buying BBCA',
    ]);

    $transaction = Transaction::where('asset_id', $asset->id)->first();
    expect($transaction)->not->toBeNull();

    // Now delete this transaction
    $response = $this->actingAs($user)->delete(route('transactions.destroy', $transaction));
    $response->assertRedirect(route('transactions.index'));

    // Check that asset quantity and purchase price are reverted
    $asset->refresh();
    expect((float) $asset->quantity)->toBe(100.0);
    expect((float) $asset->purchase_price)->toBe(8000.0);
});

it('reverts asset quantity and price when a linked transaction is updated', function () {
    $user = User::factory()->create();
    $account = Account::create([
        'name' => 'Main Account',
        'type' => 'bank',
        'balance' => 10000000,
    ]);
    $category = Category::create([
        'name' => 'Investasi',
        'type' => 'expense',
        'color' => '#000000',
    ]);
    $asset = Asset::create([
        'user_id' => $user->id,
        'type' => 'stock',
        'name' => 'BBCA',
        'quantity' => 100,
        'purchase_price' => 8000,
        'current_price' => 9000,
        'platform' => 'Nanovest',
    ]);

    // Create a transaction
    $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'asset_id' => $asset->id,
        'amount' => 1000000, // Buy for 1,000,000
        'quantity' => 100,      // Buy 100 more
        'type' => 'expense',
        'date' => now()->format('Y-m-d'),
        'description' => 'Buying BBCA',
    ]);

    $transaction = Transaction::where('asset_id', $asset->id)->first();

    // Edit transaction: change amount to 2,000,000
    $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'asset_id' => $asset->id,
        'quantity' => 100,
        'amount' => 2000000, // Updated amount
        'type' => 'expense',
        'date' => now()->format('Y-m-d'),
    ]);

    $response->assertRedirect(route('transactions.index'));

    $asset->refresh();
    // Reverted old and applied new:
    // Original Qty: 100, original Purchase Price: 8000. Total cost: 800,000
    // Reverted purchase: Qty: 100, Price: 8000
    // New purchase applied: amount 2,000,000, quantity 100.
    // Total cost now: 800,000 + 2,000,000 = 2,800,000. New Qty: 200.
    // Avg Price: 2,800,000 / 200 = 14000.
    expect((float) $asset->quantity)->toBe(200.0);
    expect((float) $asset->purchase_price)->toBe(14000.0);
});

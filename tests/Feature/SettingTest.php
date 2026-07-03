<?php

use App\Models\Account;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\SavingsGoal;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\User;

test('settings screen can be rendered for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/settings');

    $response->assertSuccessful();
});

test('users can update their settings', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/settings', [
        'reports_enabled' => 1,
        'reports_send_date' => 'start_of_month',
    ]);

    $response->assertRedirect(route('settings.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('settings', [
        'user_id' => $user->id,
        'key' => 'reports_enabled',
        'value' => '1',
    ]);

    $this->assertDatabaseHas('settings', [
        'user_id' => $user->id,
        'key' => 'reports_send_date',
        'value' => 'start_of_month',
    ]);
});

test('users can clear all data in the system and re-seed defaults', function () {
    $user = User::factory()->create();

    // Create accounts, categories, tags, transactions, budgets, savings goals, assets, recurring transactions
    $account = Account::create(['name' => 'Custom Bank', 'type' => 'bank', 'balance' => 5000, 'icon' => 'bank', 'color' => '#000']);
    $category = Category::create(['name' => 'Custom Category', 'type' => 'income', 'icon' => 'cash', 'color' => '#000']);
    $tag = Tag::create(['name' => 'Custom Tag', 'color' => '#000']);
    $asset = Asset::create([
        'name' => 'Custom Asset',
        'type' => 'crypto',
        'platform' => 'Binance',
        'quantity' => 1.5,
        'purchase_price' => 1000,
        'current_price' => 1200,
    ]);
    $goal = SavingsGoal::create(['name' => 'Goal', 'target_amount' => 1000, 'current_amount' => 100]);

    $transaction = Transaction::create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'asset_id' => null,
        'savings_goal_id' => null,
        'type' => 'income',
        'amount' => 100,
        'date' => today(),
        'description' => 'Test',
    ]);

    $budget = Budget::create(['category_id' => $category->id, 'amount' => 500, 'period' => 'monthly']);
    $recurring = RecurringTransaction::create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'type' => 'income',
        'amount' => 100,
        'description' => 'Recurring',
        'frequency' => 'monthly',
        'next_due_date' => today()->addMonth(),
        'is_active' => true,
    ]);

    // Assert database counts before clear
    expect(Account::count())->toBe(1);
    expect(Category::count())->toBe(1);
    expect(Tag::count())->toBe(1);
    expect(Asset::count())->toBe(1);
    expect(Transaction::count())->toBe(1);
    expect(Budget::count())->toBe(1);
    expect(RecurringTransaction::count())->toBe(1);
    expect(SavingsGoal::count())->toBe(1);

    // Call clear-data route
    $response = $this->actingAs($user)->post('/settings/clear-data');

    $response->assertRedirect(route('settings.index'));
    $response->assertSessionHas('success');

    // Assert custom data is deleted
    expect(Transaction::count())->toBe(0);
    expect(Budget::count())->toBe(0);
    expect(RecurringTransaction::count())->toBe(0);
    expect(SavingsGoal::count())->toBe(0);
    expect(Asset::count())->toBe(0);
    expect(Tag::count())->toBe(0);

    // Assert defaults are re-seeded (3 accounts and 9 categories)
    expect(Account::count())->toBe(3);
    expect(Category::count())->toBe(9);

    // Assert custom created accounts/categories are gone
    expect(Account::where('name', 'Custom Bank')->exists())->toBeFalse();
    expect(Category::where('name', 'Custom Category')->exists())->toBeFalse();
});

<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists savings goals for authenticated users', function () {
    $goal = SavingsGoal::factory()->create();

    $this->actingAs($this->user)
        ->get(route('savings-goals.index'))
        ->assertSuccessful()
        ->assertSee($goal->name);
});

it('allows adding a new savings goal', function () {
    $this->actingAs($this->user)
        ->post(route('savings-goals.store'), [
            'name' => 'New Car Fund',
            'target_amount' => 150000000.00,
            'current_amount' => 10000000.00,
            'deadline' => '2027-12-31',
        ])
        ->assertRedirect(route('savings-goals.index'));

    $this->assertDatabaseHas('savings_goals', [
        'name' => 'New Car Fund',
        'target_amount' => 150000000.00,
        'current_amount' => 10000000.00,
        'status' => 'active',
    ]);
});

it('allows updating an existing savings goal', function () {
    $goal = SavingsGoal::factory()->create([
        'name' => 'Car Fund',
        'target_amount' => 10000000,
        'current_amount' => 1000000,
    ]);

    $this->actingAs($this->user)
        ->put(route('savings-goals.update', $goal), [
            'name' => 'Updated Car Fund',
            'target_amount' => 12000000,
            'current_amount' => 2000000,
            'status' => 'active',
        ])
        ->assertRedirect(route('savings-goals.index'));

    $this->assertDatabaseHas('savings_goals', [
        'id' => $goal->id,
        'name' => 'Updated Car Fund',
        'target_amount' => 12000000,
        'current_amount' => 2000000,
    ]);
});

it('allows deleting a savings goal', function () {
    $goal = SavingsGoal::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('savings-goals.destroy', $goal))
        ->assertRedirect(route('savings-goals.index'));

    $this->assertDatabaseMissing('savings_goals', [
        'id' => $goal->id,
    ]);
});

it('adjusts current_amount when transaction is created, updated, or deleted', function () {
    $goal = SavingsGoal::factory()->create([
        'target_amount' => 1000000,
        'current_amount' => 500000,
    ]);

    $account = Account::create([
        'name' => 'Bank BCA',
        'type' => 'bank',
        'balance' => 5000000,
    ]);

    $category = Category::create([
        'name' => 'Savings',
        'type' => 'expense',
        'color' => '#6366f1',
    ]);

    // 1. Create linked expense transaction (+ contribution)
    $this->actingAs($this->user)
        ->post(route('transactions.store'), [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 100000,
            'type' => 'expense',
            'date' => now()->toDateString(),
            'savings_goal_id' => $goal->id,
        ])
        ->assertRedirect(route('transactions.index'));

    // current_amount should be 500000 + 100000 = 600000
    expect($goal->fresh()->current_amount)->toEqual(600000);

    $transaction = Transaction::where('savings_goal_id', $goal->id)->first();

    // 2. Update transaction amount to 200000 (+100000 contribution adjustment)
    $this->actingAs($this->user)
        ->put(route('transactions.update', $transaction), [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 200000,
            'type' => 'expense',
            'date' => now()->toDateString(),
            'savings_goal_id' => $goal->id,
        ])
        ->assertRedirect(route('transactions.index'));

    // current_amount should be 500000 + 200000 = 700000
    expect($goal->fresh()->current_amount)->toEqual(700000);

    // 3. Delete transaction (-200000 contribution)
    $this->actingAs($this->user)
        ->delete(route('transactions.destroy', $transaction))
        ->assertRedirect(route('transactions.index'));

    // current_amount should return back to 500000
    expect($goal->fresh()->current_amount)->toEqual(500000);
});

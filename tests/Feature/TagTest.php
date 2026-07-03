<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists tags for authenticated users', function () {
    $tags = Tag::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->get(route('tags.index'))
        ->assertSuccessful()
        ->assertSee($tags->first()->name);
});

it('allows adding a new tag', function () {
    $this->actingAs($this->user)
        ->post(route('tags.store'), [
            'name' => 'Monthly Expense',
            'color' => '#ef4444',
        ])
        ->assertRedirect(route('tags.index'));

    $this->assertDatabaseHas('tags', [
        'name' => 'Monthly Expense',
        'color' => '#ef4444',
    ]);
});

it('allows updating an existing tag', function () {
    $tag = Tag::factory()->create(['name' => 'Old Tag']);

    $this->actingAs($this->user)
        ->put(route('tags.update', $tag), [
            'name' => 'New Tag',
            'color' => '#10b981',
        ])
        ->assertRedirect(route('tags.index'));

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'name' => 'New Tag',
        'color' => '#10b981',
    ]);
});

it('allows deleting a tag', function () {
    $tag = Tag::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('tags.destroy', $tag))
        ->assertRedirect(route('tags.index'));

    $this->assertDatabaseMissing('tags', [
        'id' => $tag->id,
    ]);
});

it('can sync tags to transactions and filter by tag', function () {
    $tag1 = Tag::factory()->create();
    $tag2 = Tag::factory()->create();

    $account = Account::create([
        'name' => 'Bank BCA',
        'type' => 'bank',
        'balance' => 5000000,
    ]);

    $category = Category::create([
        'name' => 'Makanan',
        'type' => 'expense',
        'color' => '#f43f5e',
    ]);

    // Create a transaction with tag1
    $transaction1 = Transaction::create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => 50000,
        'date' => now()->toDateString(),
        'description' => 'Beli Makan Siang',
    ]);
    $transaction1->tags()->sync([$tag1->id]);

    // Create a transaction with tag2
    $transaction2 = Transaction::create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => 30000,
        'date' => now()->toDateString(),
        'description' => 'Beli Kopi',
    ]);
    $transaction2->tags()->sync([$tag2->id]);

    // Test transaction edit sync via request
    $this->actingAs($this->user)
        ->put(route('transactions.update', $transaction1), [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 50000,
            'type' => 'expense',
            'date' => now()->toDateString(),
            'tags' => [$tag2->id], // switch to tag2
        ])
        ->assertRedirect(route('transactions.index'));

    expect($transaction1->fresh()->tags->pluck('id')->toArray())
        ->toContain($tag2->id)
        ->not->toContain($tag1->id);

    // Test transactions index list filtering by tag2
    $this->actingAs($this->user)
        ->get(route('transactions.index', ['tag_id' => $tag2->id]))
        ->assertSuccessful()
        ->assertSee('Beli Makan Siang');
});

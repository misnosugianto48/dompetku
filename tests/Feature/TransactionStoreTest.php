<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    config(['filesystems.receipts_disk' => 'public']);
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

    $response = $this->post(route('transactions.store'), [
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

    $response = $this->post(route('transactions.store'), [
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

it('fails to store a transaction when date is missing', function () {
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

    $response = $this->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'description' => 'Lunch',
    ]);

    $response->assertSessionHasErrors(['date']);
});

it('redirects to redirect_to parameter if provided', function () {
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

    $response = $this->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Lunch',
        'redirect_to' => route('dashboard'),
    ]);

    $response->assertRedirect(route('dashboard'));
});

it('stores a transaction with a receipt and saves the path', function () {
    Storage::fake('public');

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

    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Lunch',
        'receipt' => $file,
    ]);

    $response->assertRedirect(route('transactions.index'));

    $transaction = Transaction::first();
    expect($transaction->receipt_path)->not->toBeNull();
    Storage::disk('public')->assertExists($transaction->receipt_path);
});

it('updates a transaction and can replace or delete the receipt', function () {
    Storage::fake('public');

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

    $oldFile = UploadedFile::fake()->image('receipt1.jpg');
    $oldPath = $oldFile->store('receipts', 'public');

    $transaction = Transaction::create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Lunch',
        'receipt_path' => $oldPath,
    ]);

    Storage::disk('public')->assertExists($oldPath);

    // Replace receipt
    $newFile = UploadedFile::fake()->image('receipt2.jpg');
    $response = $this->put(route('transactions.update', $transaction), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Lunch Updated',
        'receipt' => $newFile,
    ]);

    $response->assertRedirect(route('transactions.index'));
    $transaction->refresh();

    expect($transaction->receipt_path)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($transaction->receipt_path);

    // Delete receipt
    $response = $this->put(route('transactions.update', $transaction), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Lunch Updated Again',
        'delete_receipt' => 1,
    ]);

    $transaction->refresh();
    expect($transaction->receipt_path)->toBeNull();
});

it('deletes the receipt file when the transaction is deleted', function () {
    Storage::fake('public');

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

    $file = UploadedFile::fake()->image('receipt.jpg');
    $path = $file->store('receipts', 'public');

    $transaction = Transaction::create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 250000,
        'type' => 'expense',
        'date' => now()->toDateString(),
        'description' => 'Lunch',
        'receipt_path' => $path,
    ]);

    Storage::disk('public')->assertExists($path);

    $response = $this->delete(route('transactions.destroy', $transaction));
    $response->assertRedirect(route('transactions.index'));

    Storage::disk('public')->assertMissing($path);
});

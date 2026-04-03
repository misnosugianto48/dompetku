<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use Illuminate\Database\Seeder;

class RecurringTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankAccount = Account::where('type', 'bank')->first();
        $billCategory = Category::where('name', 'Bills')->first();

        if ($bankAccount && $billCategory) {
            RecurringTransaction::create([
                'account_id' => $bankAccount->id,
                'category_id' => $billCategory->id,
                'amount' => 150000,
                'type' => 'expense',
                'description' => 'Internet Subscription',
                'frequency' => 'monthly',
                'next_due_date' => now()->addMonth()->startOfMonth(),
                'is_active' => true,
            ]);
        }
    }
}

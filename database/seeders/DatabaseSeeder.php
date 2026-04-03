<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // Default Accounts
        Account::create(['name' => 'Cash', 'type' => 'cash', 'balance' => 0, 'icon' => 'banknotes', 'color' => '#10b981']);
        Account::create(['name' => 'Main Bank', 'type' => 'bank', 'balance' => 1000000, 'icon' => 'building-library', 'color' => '#3b82f6']);
        Account::create(['name' => 'E-Wallet', 'type' => 'wallet', 'balance' => 0, 'icon' => 'wallet', 'color' => '#8b5cf6']);

        // Income Categories
        Category::create(['name' => 'Salary', 'type' => 'income', 'icon' => 'currency-dollar', 'color' => '#10b981']);
        Category::create(['name' => 'Bonus', 'type' => 'income', 'icon' => 'gift', 'color' => '#f59e0b']);
        Category::create(['name' => 'Investment', 'type' => 'income', 'icon' => 'chart-bar', 'color' => '#3b82f6']);

        // Expense Categories
        Category::create(['name' => 'Food & Beverage', 'type' => 'expense', 'icon' => 'cake', 'color' => '#ef4444']);
        Category::create(['name' => 'Shopping', 'type' => 'expense', 'icon' => 'shopping-bag', 'color' => '#ec4899']);
        Category::create(['name' => 'Transport', 'type' => 'expense', 'icon' => 'truck', 'color' => '#f59e0b']);
        Category::create(['name' => 'Bills', 'type' => 'expense', 'icon' => 'receipt-refund', 'color' => '#6366f1']);
        Category::create(['name' => 'Health', 'type' => 'expense', 'icon' => 'heart', 'color' => '#ef4444']);
        Category::create(['name' => 'Entertainment', 'type' => 'expense', 'icon' => 'sparkles', 'color' => '#8b5cf6']);
    }
}

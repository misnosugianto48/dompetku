<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foodCategory = Category::where('name', 'Food & Beverage')->first();
        $transportCategory = Category::where('name', 'Transport')->first();

        if ($foodCategory) {
            Budget::create([
                'category_id' => $foodCategory->id,
                'amount' => 2000000,
                'period' => 'monthly',
            ]);
        }

        if ($transportCategory) {
            Budget::create([
                'category_id' => $transportCategory->id,
                'amount' => 500000,
                'period' => 'monthly',
            ]);
        }
    }
}

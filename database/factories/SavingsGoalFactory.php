<?php

namespace Database\Factories;

use App\Models\SavingsGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavingsGoal>
 */
class SavingsGoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'target_amount' => 5000000.00,
            'current_amount' => 100000.00,
            'deadline' => $this->faker->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'status' => 'active',
        ];
    }
}

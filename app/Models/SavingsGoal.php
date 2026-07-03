<?php

namespace App\Models;

use Database\Factories\SavingsGoalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends Model
{
    /** @use HasFactory<SavingsGoalFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public static function adjustAmount(?int $goalId, string $type, float $amount, bool $isReversal = false): void
    {
        if (! $goalId) {
            return;
        }

        $goal = self::find($goalId);
        if (! $goal) {
            return;
        }

        $multiplier = $isReversal ? -1 : 1;

        if ($type === 'expense' || $type === 'transfer') {
            $goal->current_amount += ($amount * $multiplier);
        } elseif ($type === 'income') {
            $goal->current_amount -= ($amount * $multiplier);
        }

        $goal->status = $goal->current_amount >= $goal->target_amount ? 'completed' : 'active';
        $goal->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency',
        'rate',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
        ];
    }

    public static function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        // Convert to IDR (base) first
        $amountInIdr = $amount;
        if ($from !== 'IDR') {
            $fromRate = self::where('currency', $from)->first()?->rate ?? 1.0;
            $amountInIdr = $amount * (float) $fromRate;
        }

        // Convert from IDR to target currency
        if ($to === 'IDR') {
            return $amountInIdr;
        }

        $toRate = self::where('currency', $to)->first()?->rate ?? 1.0;

        return (float) $toRate > 0 ? ($amountInIdr / (float) $toRate) : $amountInIdr;
    }
}

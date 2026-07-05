<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'type',
        'balance',
        'currency',
        'icon',
        'color',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFormattedBalanceAttribute(): string
    {
        $symbol = match ($this->currency) {
            'USD' => '$',
            'SGD' => 'S$',
            'EUR' => '€',
            'JPY' => '¥',
            default => 'Rp',
        };

        if ($this->currency === 'IDR') {
            return 'Rp '.number_format($this->balance, 0, ',', '.');
        }

        // Decimal formatting for non-IDR
        $formatted = $symbol.' '.number_format($this->balance, 2, '.', ',');

        // Find exchange rate
        $rate = ExchangeRate::where('currency', $this->currency)->first()?->rate;
        if ($rate) {
            $idrEquivalent = (float) $this->balance * (float) $rate;
            $formatted .= ' (Rp '.number_format($idrEquivalent, 0, ',', '.').')';
        }

        return $formatted;
    }
}

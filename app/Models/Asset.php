<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'type',
        'platform',
        'quantity',
        'purchase_price',
        'current_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:8',
            'purchase_price' => 'decimal:4',
            'current_price' => 'decimal:4',
        ];
    }

    public function priceHistories()
    {
        return $this->hasMany(AssetPriceHistory::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'type',
        'quantity',
        'purchase_price',
        'current_price',
    ];

    public function priceHistories()
    {
        return $this->hasMany(AssetPriceHistory::class);
    }
}

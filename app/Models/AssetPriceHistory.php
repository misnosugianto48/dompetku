<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetPriceHistory extends Model
{
    protected $fillable = [
        'asset_id',
        'price',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

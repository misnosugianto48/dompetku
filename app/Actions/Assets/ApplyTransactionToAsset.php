<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use App\Models\AssetPriceHistory;

class ApplyTransactionToAsset
{
    public function handle(Asset $asset, string $type, float $amount, float $quantity, string $date): void
    {
        if ($type === 'expense') {
            $totalCost = ($asset->quantity * $asset->purchase_price) + $amount;

            $asset->quantity += $quantity;

            if ($asset->quantity > 0) {
                $asset->purchase_price = $totalCost / $asset->quantity;
            }

            $asset->current_price = $amount / $quantity;
        } else {
            $asset->quantity -= $quantity;
        }

        $asset->save();

        AssetPriceHistory::create([
            'asset_id' => $asset->id,
            'price' => $asset->current_price,
            'date' => $date,
        ]);
    }
}

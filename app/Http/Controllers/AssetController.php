<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetPriceHistory;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::all()->map(function (Asset $asset) {
            $asset->total_value = $asset->quantity * $asset->current_price;
            $asset->total_invested = $asset->quantity * $asset->purchase_price;
            $asset->gain_loss = $asset->total_value - $asset->total_invested;
            $asset->gain_loss_percent = $asset->total_invested > 0
                ? (($asset->gain_loss / $asset->total_invested) * 100)
                : 0;

            return $asset;
        });

        $totalPortfolioValue = $assets->sum('total_value');
        $totalInvested = $assets->sum('total_invested');

        return view('assets.index', compact('assets', 'totalPortfolioValue', 'totalInvested'));
    }

    public function store(StoreAssetRequest $request)
    {
        $validated = $request->validated();

        $asset = Asset::create($validated);

        AssetPriceHistory::create([
            'asset_id' => $asset->id,
            'price' => $validated['current_price'],
            'date' => now()->toDateString(),
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset added successfully.');
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $validated = $request->validated();

        $asset->update($validated);

        AssetPriceHistory::create([
            'asset_id' => $asset->id,
            'price' => $validated['current_price'],
            'date' => now()->toDateString(),
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset updated.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset deleted.');
    }
}

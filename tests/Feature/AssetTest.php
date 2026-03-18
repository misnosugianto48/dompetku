<?php

use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create an asset with a platform and high precision decimals', function () {
    $response = $this->post(route('assets.store'), [
        'name' => 'Nanovest Bitcoin',
        'type' => 'crypto',
        'platform' => 'Nanovest',
        'quantity' => '0.00050341',
        'purchase_price' => '1000000000.50',
        'current_price' => '1100000000.75',
    ]);

    $response->assertRedirect(route('assets.index'));

    $this->assertDatabaseHas('assets', [
        'name' => 'Nanovest Bitcoin',
        'platform' => 'Nanovest',
        'quantity' => '0.00050341',
    ]);
});

it('can update an asset price with high precision', function () {
    $asset = Asset::create([
        'name' => 'Apple stock',
        'type' => 'stock',
        'platform' => 'Nanovest',
        'quantity' => 1.5,
        'purchase_price' => 150.25,
        'current_price' => 150.25,
    ]);

    $response = $this->put(route('assets.update', $asset), [
        'quantity' => 1.5,
        'current_price' => '155.8099',
    ]);

    $response->assertRedirect(route('assets.index'));

    $this->assertDatabaseHas('assets', [
        'id' => $asset->id,
        'current_price' => '155.8099',
    ]);
});

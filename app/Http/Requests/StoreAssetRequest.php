<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:stock,gold,mutual_fund,crypto,bond'],
            'platform' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'purchase_price' => ['required', 'numeric', 'gt:0'],
            'current_price' => ['required', 'numeric', 'gt:0'],
        ];
    }
}

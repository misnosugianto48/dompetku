<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'type' => ['required', 'in:income,expense'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
            'quantity' => ['nullable', 'numeric', 'gt:0', 'required_with:asset_id'],
        ];
    }
}

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
            'destination_account_id' => ['nullable', 'exists:accounts,id', 'required_if:type,transfer'],
            'category_id' => ['nullable', 'exists:categories,id', 'required_unless:type,transfer'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'type' => ['required', 'in:income,expense,transfer'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'quantity' => ['nullable', 'numeric', 'gt:0', 'required_with:asset_id'],
        ];
    }
}

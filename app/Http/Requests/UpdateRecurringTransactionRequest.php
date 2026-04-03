<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecurringTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:expense,income',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'next_due_date' => 'required|date',
            'is_active' => 'nullable',
        ];
    }
}

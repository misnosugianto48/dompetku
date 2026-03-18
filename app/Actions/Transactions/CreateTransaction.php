<?php

namespace App\Actions\Transactions;

use App\Models\Transaction;
use Illuminate\Support\Arr;

class CreateTransaction
{
    /**
     * @param  array{
     *   account_id:int|string,
     *   category_id:int|string,
     *   asset_id?:int|string|null,
     *   amount:numeric-string|int|float,
     *   type:'income'|'expense',
     *   date:string,
     *   description?:string|null,
     * }  $data
     */
    public function handle(array $data): Transaction
    {
        return Transaction::create(Arr::only($data, [
            'account_id',
            'category_id',
            'asset_id',
            'amount',
            'type',
            'date',
            'description',
        ]));
    }
}

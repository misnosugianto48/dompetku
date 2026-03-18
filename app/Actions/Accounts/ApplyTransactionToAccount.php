<?php

namespace App\Actions\Accounts;

use App\Models\Account;

class ApplyTransactionToAccount
{
    public function handle(Account $account, string $type, float $amount): void
    {
        if ($type === 'income') {
            $account->increment('balance', $amount);

            return;
        }

        $account->decrement('balance', $amount);
    }
}

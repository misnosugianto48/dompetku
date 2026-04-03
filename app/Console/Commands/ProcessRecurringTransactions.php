<?php

namespace App\Console\Commands;

use App\Actions\Accounts\ApplyTransactionToAccount;
use App\Actions\Transactions\CreateTransaction;
use App\Models\RecurringTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:process-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all active recurring transactions seamlessly filling missed obligations recursively';

    /**
     * Execute the console command.
     */
    public function handle(CreateTransaction $createTransaction, ApplyTransactionToAccount $applyTransaction)
    {
        $today = Carbon::today()->toDateString();

        $dueTransactions = RecurringTransaction::where('is_active', true)
            ->where('next_due_date', '<=', $today)
            ->get();

        if ($dueTransactions->isEmpty()) {
            $this->info('No recurring transactions due today.');

            return;
        }

        $count = 0;
        foreach ($dueTransactions as $recurring) {
            DB::transaction(function () use ($recurring, $createTransaction, $applyTransaction, $today) {
                $processDate = $recurring->next_due_date->copy();

                while ($processDate->toDateString() <= $today) {
                    $createTransaction->handle([
                        'account_id' => $recurring->account_id,
                        'category_id' => $recurring->category_id,
                        'type' => $recurring->type,
                        'amount' => $recurring->amount,
                        'date' => $processDate->toDateString(),
                        'description' => trim($recurring->description.' (Auto)'),
                    ]);

                    // Note: Ensure the freshest account model is grabbed and manipulated directly
                    $account = $recurring->account()->lockForUpdate()->first();
                    $applyTransaction->handle($account, $recurring->type, (float) $recurring->amount);

                    switch ($recurring->frequency) {
                        case 'daily':
                            $processDate->addDay();
                            break;
                        case 'weekly':
                            $processDate->addWeek();
                            break;
                        case 'monthly':
                            $processDate->addMonth();
                            break;
                        case 'yearly':
                            $processDate->addYear();
                            break;
                    }
                }

                $recurring->update(['next_due_date' => $processDate->toDateString()]);
            });
            $count++;
        }

        $this->info("Processed {$count} recurring transactions effectively catching up all trailing recurrences!");
    }
}

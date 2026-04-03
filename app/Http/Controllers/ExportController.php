<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $transactions = Transaction::with(['account', 'category', 'asset', 'destinationAccount'])->latest('date')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=dompetku-transactions.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Date', 'Type', 'Amount', 'Account', 'Destination Account', 'Category', 'Asset', 'Asset Price', 'Description', 'Notes'];

        $callback = function () use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $transaction) {
                $row = [
                    $transaction->id,
                    $transaction->date ? $transaction->date->format('Y-m-d') : '',
                    $transaction->type,
                    $transaction->amount,
                    $transaction->account->name ?? '',
                    $transaction->destinationAccount->name ?? '',
                    $transaction->category->name ?? '',
                    $transaction->asset->name ?? '',
                    $transaction->asset_price ?? '',
                    $transaction->description ?? '',
                    $transaction->notes ?? '',
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

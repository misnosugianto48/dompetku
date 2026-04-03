<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! $header || ! in_array('ID', $header)) {
            return back()->with('error', 'Invalid CSV format natively parsed. Ensure it originates from the Dompetku Export generator.');
        }

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($header) !== count($row)) {
                    continue;
                }

                $data = array_combine($header, $row);

                if (empty($data['Account'])) {
                    continue;
                }

                $account = Account::firstOrCreate(['name' => $data['Account']], ['type' => 'cash', 'balance' => 0]);
                $category = ! empty($data['Category']) ? Category::firstOrCreate(['name' => $data['Category']], ['type' => $data['Type'], 'color' => '#64748b', 'icon' => 'tag']) : null;
                $destination = ! empty($data['Destination Account']) ? Account::firstOrCreate(['name' => $data['Destination Account']], ['type' => 'cash', 'balance' => 0]) : null;

                Transaction::updateOrCreate(
                    ['id' => $data['ID']],
                    [
                        'date' => $data['Date'],
                        'type' => $data['Type'],
                        'amount' => $data['Amount'],
                        'account_id' => $account->id,
                        'destination_account_id' => $destination?->id,
                        'category_id' => $category?->id,
                        'description' => $data['Description'],
                        'notes' => $data['Notes'],
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Import failed gracefully natively: '.$e->getMessage());
        }

        fclose($handle);

        return back()->with('success', 'Transactions imported flawlessly securely.');
    }
}

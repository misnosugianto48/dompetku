<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
use App\Models\AssetPriceHistory;
use App\Models\Budget;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\SavingsGoal;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $settings = Setting::where('user_id', $user->id)->pluck('value', 'key')->toArray();

        $reportsEnabled = $settings['reports_enabled'] ?? '0';
        $reportsSendDate = $settings['reports_send_date'] ?? 'end_of_month'; // 'end_of_month' or 'start_of_month'

        return view('settings.index', compact('reportsEnabled', 'reportsSendDate'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'reports_enabled' => 'nullable|boolean',
            'reports_send_date' => 'required|in:end_of_month,start_of_month',
        ]);

        $reportsEnabled = $request->has('reports_enabled') ? '1' : '0';

        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'reports_enabled'],
            ['value' => $reportsEnabled]
        );

        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'reports_send_date'],
            ['value' => $validated['reports_send_date']]
        );

        return redirect()->route('settings.index')->with('success', 'Application settings updated securely.');
    }

    public function clearData(Request $request): RedirectResponse
    {
        DB::transaction(function () {
            // Delete transactional and asset data in foreign-key-safe order
            DB::table('transaction_tag')->delete();
            Transaction::query()->delete();
            Budget::query()->delete();
            SavingsGoal::query()->delete();
            RecurringTransaction::query()->delete();
            AssetPriceHistory::query()->delete();
            Asset::query()->delete();
            Account::query()->delete();
            Category::query()->delete();
            Tag::query()->delete();

            // Re-seed default accounts
            Account::create(['name' => 'Cash', 'type' => 'cash', 'balance' => 0, 'icon' => 'banknotes', 'color' => '#10b981']);
            Account::create(['name' => 'Main Bank', 'type' => 'bank', 'balance' => 0, 'icon' => 'building-library', 'color' => '#3b82f6']);
            Account::create(['name' => 'E-Wallet', 'type' => 'wallet', 'balance' => 0, 'icon' => 'wallet', 'color' => '#8b5cf6']);

            // Re-seed default categories
            Category::create(['name' => 'Salary', 'type' => 'income', 'icon' => 'currency-dollar', 'color' => '#10b981']);
            Category::create(['name' => 'Bonus', 'type' => 'income', 'icon' => 'gift', 'color' => '#f59e0b']);
            Category::create(['name' => 'Investment', 'type' => 'income', 'icon' => 'chart-bar', 'color' => '#3b82f6']);

            Category::create(['name' => 'Food & Beverage', 'type' => 'expense', 'icon' => 'cake', 'color' => '#ef4444']);
            Category::create(['name' => 'Shopping', 'type' => 'expense', 'icon' => 'shopping-bag', 'color' => '#ec4899']);
            Category::create(['name' => 'Transport', 'type' => 'expense', 'icon' => 'truck', 'color' => '#f59e0b']);
            Category::create(['name' => 'Bills', 'type' => 'expense', 'icon' => 'receipt-refund', 'color' => '#6366f1']);
            Category::create(['name' => 'Health', 'type' => 'expense', 'icon' => 'heart', 'color' => '#ef4444']);
            Category::create(['name' => 'Entertainment', 'type' => 'expense', 'icon' => 'sparkles', 'color' => '#8b5cf6']);
        });

        return redirect()->route('settings.index')->with('success', 'All transactional history, assets, budgets, and savings goals have been securely cleared.');
    }
}

<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions/transfer', [TransactionController::class, 'transfer'])->name('transactions.transfer');

    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'update', 'edit', 'destroy']);

    Route::get('/export/transactions', [ExportController::class, 'export'])->name('transactions.export');
    Route::post('/import/transactions', [ImportController::class, 'import'])->name('transactions.import');

    Route::resource('accounts', AccountController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::resource('assets', AssetController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('categories', CategoryController::class);
    Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('recurring', RecurringTransactionController::class)->except(['create', 'edit', 'show']);
    Route::patch('recurring/{recurring}/toggle', [RecurringTransactionController::class, 'toggle'])->name('recurring.toggle');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

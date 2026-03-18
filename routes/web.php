<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('accounts', AccountController::class)->only(['index', 'store', 'destroy']);
Route::resource('assets', AssetController::class)->only(['index', 'store', 'update', 'destroy']);

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

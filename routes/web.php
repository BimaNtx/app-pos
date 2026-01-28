<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Livewire\Categories;
use App\Livewire\Dashboard;
use App\Livewire\Employees;
use App\Livewire\Expenses;

use App\Livewire\PosPage;
use App\Livewire\Products;
use App\Livewire\Reports;
use App\Livewire\Settings;
use App\Livewire\Transactions;
use Illuminate\Support\Facades\Route;

// Root redirects to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth Routes (only accessible when logged in)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/pos', PosPage::class)->name('pos');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/products', Products::class)->name('products');
    Route::get('/categories', Categories::class)->name('categories');
    Route::get('/transactions', Transactions::class)->name('transactions');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/settings', Settings::class)->name('settings');

    Route::get('/employees', Employees::class)->name('employees');
    Route::get('/expenses', Expenses::class)->name('expenses');
    Route::get('/reports/download-pdf/{period}', [ReportController::class, 'downloadPdf'])->name('reports.download-pdf');
});


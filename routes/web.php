<?php

use App\Livewire\Dashboard;
use App\Livewire\PosPage;
use App\Livewire\Products;
use App\Livewire\Reports;
use App\Livewire\Settings;
use App\Livewire\Transactions;
use Illuminate\Support\Facades\Route;

Route::get('/', PosPage::class)->name('pos');
Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/products', Products::class)->name('products');
Route::get('/transactions', Transactions::class)->name('transactions');
Route::get('/reports', Reports::class)->name('reports');
Route::get('/settings', Settings::class)->name('settings');

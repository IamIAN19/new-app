<?php

use App\Http\Controllers\AccountTitleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesCategoryController;
use App\Http\Controllers\SupplierController;
use App\Models\SalesCategory;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/create-invoice', [InvoiceController::class, 'create'])->name('invoice.create');

    Route::prefix('company')->controller(CompanyController::class)->group(function() {
        Route::get('/', 'index')->name('company.index');
    });

    Route::prefix('sales')->controller(SalesCategoryController::class)->group(function() {
        Route::get('/', 'index')->name('sales.index');
    });

    Route::prefix('accounts')->controller(AccountTitleController::class)->group(function() {
        Route::get('/', 'index')->name('accounts.index');
    });

    Route::prefix('supplier')->controller(SupplierController::class)->group(function() {
        Route::get('/', 'index')->name('supplier.index');
    });

    Route::prefix('repor')->controller(ReportsController::class)->group(function() {
        Route::get('/', 'index')->name('reports.index');
    });

});

require __DIR__.'/auth.php';

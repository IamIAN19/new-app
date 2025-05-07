<?php

use App\Http\Controllers\AccountTitleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Department;
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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('invoices')->controller(InvoiceController::class)->group(function() {
        Route::get('/', 'index')->name('invoices.index');
        Route::get('/create', 'create')->name('invoices.create');
        Route::post('/create', 'store')->name('invoices.store');
        Route::get('/{invoice}/show', 'show')->name('invoices.show');
        Route::post('/update', 'update')->name('invoices.update');
        Route::post('/delete', 'delete')->name('invoices.delete');
        Route::get('/fetch-account-section', 'fetchAccountSection')->name('invoices.fetch-account-section');
        Route::get('/fetch-content', 'fetchContent')->name('invoices.fetch-content');
    });

    Route::prefix('company')->controller(CompanyController::class)->group(function() {
        Route::get('/', 'index')->name('company.index');
        Route::post('/', 'store')->name('company.store');
        Route::post('/update', 'update')->name('company.update');
        Route::post('/update-status', 'updateStatus')->name('company.update-status');
        Route::get('/fetch-content', 'fetchContent')->name('company.fetch-content');
      
    });

    Route::prefix('sales')->controller(SalesCategoryController::class)->group(function() {
        Route::get('/', 'index')->name('sales.index');
        Route::post('/', 'store')->name('sales.store');
        Route::post('/update', 'update')->name('sales.update');
        Route::get('/fetch-content', 'fetchContent')->name('sales.fetch-content');
        Route::post('/update-status', 'updateStatus')->name('sales.update-status');
      
    });

    Route::prefix('accounts')->controller(AccountTitleController::class)->group(function () {
        Route::get('/', 'index')->name('accounts.index');
        Route::post('/store', 'store')->name('accounts.store');
        Route::post('/{id}/update', 'update')->name('accounts.update');
        Route::delete('/{id}/delete', 'destroy')->name('accounts.destroy');

        Route::get('/fetch-modal', 'fetchModalBody')->name('accounts.fetch-modal');
        Route::post('/update-status', 'updateStatus')->name('accounts.update-status');
    });

    Route::prefix('supplier')->controller(SupplierController::class)->group(function() {
        Route::get('/', 'index')->name('supplier.index');
        Route::post('/', 'store')->name('supplier.store');
        Route::post('/update', 'update')->name('supplier.update');
        Route::post('/remove', 'remove')->name('supplier.remove');

        Route::get('/fetch-content', 'fetchContent')->name('supplier.fetch-content');
        Route::get('/supplier-by-tin/{tin}', 'getByTin')->name('supplier.supplier-by-tin');
    });

    Route::prefix('report')->controller(ReportsController::class)->group(function() {
        Route::get('/', 'index')->name('reports.index');
        Route::get('/fetch-content', 'fetchContent')->name('reports.fetch-content');
        Route::get('/export', 'export')->name('report.export');
        Route::get('/export-disbursement', 'exportDisbursementReport')->name('report.export-disbursement');
        Route::get('/export-cash', 'exportCashReport')->name('report.export-cash');
        Route::get('/export-purchases', 'exportPurchasesReport')->name('report.export-purchases');
        Route::get('/export-slsp', 'exportSlspReport')->name('report.export-slsp');
        Route::get('/export-departamental', 'exportDepartamentalReport')->name('report.export-departamental');
        Route::get('/export-journal', 'exportGeneralJournalReport')->name('report.export-journal');
    });

    Route::prefix('deparment')->controller(DepartmentController::class)->group(function() {
        Route::get('/', 'index')->name('department.index');
        Route::post('/store', 'store')->name('department.store');
        Route::get('/fetch-content', 'fetchContent')->name('department.fetch-content');
        Route::post('/update', 'update')->name('department.update');
        Route::post('/update-status', 'updateStatus')->name('department.update-status');
    });

    Route::prefix('users')->controller(UserController::class)->group(function() {
        Route::get('/', 'index')->name('users.index');
        Route::get('/list', 'list');
        Route::post('/store', 'store');
        Route::get('/edit/{id}', 'edit');
        Route::post('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
        Route::post('/toggle-status/{id}', 'toggleStatus');
    });

});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DueController;
use App\Http\Controllers\ExpiryController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\LedgerReportController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\PublicShopController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportHubController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC routes (login lagbe na) - sobai product dekhte & list pathate parbe
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicShopController::class, 'index'])->name('welcome');
Route::post('/order-request', [PublicShopController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('order.request');

/*
|--------------------------------------------------------------------------
| Guest routes (login / first-time register)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    // Registration is only open while there are no users (first admin setup).
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Admin home = product list with search (age '/' chilo, ekhon '/admin')
    Route::get('/admin', [ProductController::class, 'index'])->name('home');

    // Customer order requests (public page theke ashe) -> admin phone kore confirm korbe
    Route::get('/order-requests', [OrderRequestController::class, 'index'])->name('order-requests.index');
    Route::get('/order-requests/{orderRequest}', [OrderRequestController::class, 'show'])->name('order-requests.show');
    Route::patch('/order-requests/{orderRequest}/status', [OrderRequestController::class, 'updateStatus'])->name('order-requests.status');
    Route::delete('/order-requests/{orderRequest}', [OrderRequestController::class, 'destroy'])->name('order-requests.destroy');

    // Product management (add / edit / delete / list)
    Route::resource('products', ProductController::class)->except(['show']);

    // Reports (mPDF): daily / monthly / yearly
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');

    // Voucher / memo book routes
    Route::get('/memos', [MemoController::class, 'index'])->name('memos.index');
    Route::get('/memos/create', [MemoController::class, 'create'])->name('memos.create');
    Route::post('/memos', [MemoController::class, 'store'])->name('memos.store');
    Route::get('/memos/{memo}', [MemoController::class, 'show'])->name('memos.show');
    Route::get('/memos/{memo}/pdf', [MemoController::class, 'pdf'])->name('memos.pdf');
    Route::delete('/memos/{memo}', [MemoController::class, 'destroy'])->name('memos.destroy');

    // Shop management routes
    Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');
    Route::get('/shops/{shop}/edit', [ShopController::class, 'edit'])->name('shops.edit');
    Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('shops.update');
    Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');

    // Customers (directory, due balance, PDF reports)
    Route::get('/customers/report/pdf', [CustomerController::class, 'report'])->name('customers.report');
    Route::get('/customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::resource('customers', CustomerController::class);

    // Companies / suppliers
    Route::resource('companies', CompanyController::class);

    // Purchases (stock bought from companies) + paying the company
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('/purchases/{purchase}/pay', [PurchaseController::class, 'pay'])->name('purchases.pay');

    // Customer dues
    Route::get('/dues', [DueController::class, 'index'])->name('dues.index');
    Route::get('/dues/{customer}', [DueController::class, 'show'])->name('dues.show');
    Route::post('/dues/{customer}/pay', [DueController::class, 'pay'])->name('dues.pay');

    // Expiry alerts
    Route::get('/expiry', [ExpiryController::class, 'index'])->name('expiry.index');
    Route::delete('/expiry/{batch}', [ExpiryController::class, 'destroy'])->name('expiry.destroy');

    // All reports hub (memo / shop / customer / company / purchase / product reports)
    Route::get('/reports/all', [ReportHubController::class, 'index'])->name('hub.index');
    Route::get('/reports/all/pdf', [ReportHubController::class, 'pdf'])->name('hub.pdf');
    Route::get('/reports/all/count', [ReportHubController::class, 'count'])->name('hub.count');
    Route::get('/reports/count', [ReportController::class, 'count'])->name('reports.count');

    // Ledger reports (PDF)
    Route::get('/ledgers', [LedgerReportController::class, 'index'])->name('ledgers.index');
    Route::get('/ledgers/customers/pdf', [LedgerReportController::class, 'customers'])->name('ledgers.customers');
    Route::get('/ledgers/companies/pdf', [LedgerReportController::class, 'companies'])->name('ledgers.companies');

    // Staff & instruments
    Route::resource('staff', StaffController::class)->except(['show'])->parameters(['staff' => 'staff']);
    Route::resource('instruments', InstrumentController::class)->except(['show']);
});
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuotationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

        Route::middleware(['company.context', 'subscription.active'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

            Route::apiResource('clients', ClientController::class)->names([
                'index' => 'api.clients.index',
                'store' => 'api.clients.store',
                'show' => 'api.clients.show',
                'update' => 'api.clients.update',
                'destroy' => 'api.clients.destroy',
            ]);

            Route::apiResource('products', ProductController::class)->names([
                'index' => 'api.products.index',
                'store' => 'api.products.store',
                'show' => 'api.products.show',
                'update' => 'api.products.update',
                'destroy' => 'api.products.destroy',
            ]);

            Route::apiResource('invoices', InvoiceController::class)->names([
                'index' => 'api.invoices.index',
                'store' => 'api.invoices.store',
                'show' => 'api.invoices.show',
                'update' => 'api.invoices.update',
                'destroy' => 'api.invoices.destroy',
            ]);
            Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment'])->name('api.invoices.payments.store');
            Route::post('/invoices/{invoice}/expenses', [InvoiceController::class, 'recordExpense'])->name('api.invoices.expenses.store');
            Route::delete('/invoices/{invoice}/expenses/{expense}', [InvoiceController::class, 'deleteExpense'])->name('api.invoices.expenses.destroy');
            Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('api.invoices.send');
            Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('api.invoices.pdf');

            Route::apiResource('quotations', QuotationController::class)->names([
                'index' => 'api.quotations.index',
                'store' => 'api.quotations.store',
                'show' => 'api.quotations.show',
                'update' => 'api.quotations.update',
                'destroy' => 'api.quotations.destroy',
            ]);
            Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('api.quotations.convert');
            Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('api.quotations.pdf');

            Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('api.bank-accounts.index');
            Route::post('/bank-accounts', [BankAccountController::class, 'store'])->name('api.bank-accounts.store');
            Route::put('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('api.bank-accounts.update');

            Route::match(['put', 'post'], '/company', [CompanyController::class, 'update'])->name('api.company.update');

            Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
            Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('api.notifications.read-all');

            Route::post('/device-tokens', [\App\Http\Controllers\Api\DeviceTokenController::class, 'store'])->name('api.device-tokens.store');
            Route::delete('/device-tokens', [\App\Http\Controllers\Api\DeviceTokenController::class, 'destroy'])->name('api.device-tokens.destroy');

            Route::get('/billing/plans', [BillingController::class, 'plans'])->name('api.billing.plans');
            Route::get('/billing/submissions', [BillingController::class, 'index'])->name('api.billing.submissions.index');
            Route::post('/billing/submissions', [BillingController::class, 'store'])->name('api.billing.submissions.store');
            Route::get('/billing/submissions/{billingSubmission}', [BillingController::class, 'show'])->name('api.billing.submissions.show');
        });
    });
});

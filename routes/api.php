<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuotationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

        Route::middleware('company.context')->group(function () {
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
            Route::apiResource('quotations', QuotationController::class)->names([
                'index' => 'api.quotations.index',
                'store' => 'api.quotations.store',
                'show' => 'api.quotations.show',
                'update' => 'api.quotations.update',
                'destroy' => 'api.quotations.destroy',
            ]);

            Route::get('/billing/plans', [BillingController::class, 'plans'])->name('api.billing.plans');
            Route::get('/billing/submissions', [BillingController::class, 'index'])->name('api.billing.submissions.index');
            Route::post('/billing/submissions', [BillingController::class, 'store'])->name('api.billing.submissions.store');
            Route::get('/billing/submissions/{billingSubmission}', [BillingController::class, 'show'])->name('api.billing.submissions.show');
        });
    });
});

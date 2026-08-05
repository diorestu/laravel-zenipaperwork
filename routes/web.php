<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceExpenseController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\MobileWorkspaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PakasirWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicInvoiceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/public/invoices/{token}', [PublicInvoiceController::class, 'show'])->name('public.invoices.show');
Route::post('/webhooks/pakasir', PakasirWebhookController::class)->name('webhooks.pakasir');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
Route::view('/terms-of-service', 'pages.terms-of-service')->name('terms-of-service');
Route::view('/mobile', 'pwa.install')->name('pwa.install');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::get('/signin', [AuthController::class, 'loginForm'])->name('signin');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::get('/signup', [AuthController::class, 'registerForm'])->name('signup');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::view('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerification'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard')->with('success', 'Email terverifikasi.');
    })->middleware('signed')->name('verification.verify');

    Route::middleware(['company.context', 'subscription.active', 'redirect.mobile'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/mobile/app', [MobileWorkspaceController::class, 'index'])->name('mobile.app');

        Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource('quotations', QuotationController::class);
        Route::patch('/quotations/{quotation}/status', [QuotationController::class, 'status'])->name('quotations.status');
        Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
        Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');

        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
        Route::post('/calendar/sync', [CalendarController::class, 'sync'])->name('calendar.sync');

        Route::resource('invoices', InvoiceController::class);
        Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'status'])->name('invoices.status');
        Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        Route::post('/invoices/{invoice}/payments', [InvoicePaymentController::class, 'store'])->name('invoices.payments.store');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::get('/invoices/{invoice}/receipt', [InvoiceController::class, 'receipt'])->name('invoices.receipt');
        Route::post('/invoices/{invoice}/expenses', [InvoiceExpenseController::class, 'store'])->name('invoices.expenses.store');
        Route::delete('/invoices/{invoice}/expenses/{expense}', [InvoiceExpenseController::class, 'destroy'])->name('invoices.expenses.destroy');
        Route::post('/invoices/{invoice}/credit-notes', [CreditNoteController::class, 'store'])->name('invoices.credit-notes.store');
        Route::get('/credit-notes/{creditNote}', [CreditNoteController::class, 'show'])->name('credit-notes.show');
        Route::patch('/credit-notes/{creditNote}/void', [CreditNoteController::class, 'void'])->name('credit-notes.void');
        Route::get('/credit-notes/{creditNote}/pdf', [CreditNoteController::class, 'pdf'])->name('credit-notes.pdf');

        Route::get('/settings/company', [SettingsController::class, 'company'])->name('settings.company');
        Route::put('/settings/company', [SettingsController::class, 'updateCompany'])->name('settings.company.update');
        Route::get('/settings/billing', [BillingController::class, 'index'])->name('settings.billing');
        Route::get('/settings/billing/{billingSubmission}', [BillingController::class, 'show'])->name('settings.billing.show');
        Route::get('/mobile/invoices/{invoice}', [InvoiceController::class, 'mobileShow'])->name('mobile.invoices.show');
        Route::get('/mobile/billing', [BillingController::class, 'mobileIndex'])->name('mobile.billing');
        Route::get('/mobile/billing/{billingSubmission}', [BillingController::class, 'mobileShow'])->name('mobile.billing.show');
        Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
        Route::view('/settings/security', 'settings.security')->name('settings.security');
        Route::get('/settings/bank-accounts', [SettingsController::class, 'bankAccounts'])->name('settings.bank-accounts');
        Route::post('/settings/bank-accounts', [SettingsController::class, 'storeBankAccount'])->name('settings.bank-accounts.store');
        Route::put('/settings/bank-accounts/{bankAccount}', [SettingsController::class, 'updateBankAccount'])->name('settings.bank-accounts.update');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

        Route::middleware('role:super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
            Route::get('/', [SuperAdminController::class, 'index'])->name('index');
            Route::post('/billing-submissions/{billingSubmission}/confirm', [SuperAdminController::class, 'confirmBilling'])->name('billing.confirm');
            Route::patch('/billing-submissions/{billingSubmission}/activate', [SuperAdminController::class, 'activateBilling'])->name('billing.activate');
            Route::patch('/billing-submissions/{billingSubmission}/stop', [SuperAdminController::class, 'stopBilling'])->name('billing.stop');
        });
    });
});

Route::redirect('/profile', '/settings/company')->name('profile');
Route::redirect('/form-elements', '/invoices/create')->name('form-elements');
Route::redirect('/basic-tables', '/invoices')->name('basic-tables');
Route::redirect('/blank', '/')->name('blank');
Route::redirect('/error-404', '/')->name('error-404');
Route::redirect('/line-chart', '/')->name('line-chart');
Route::redirect('/bar-chart', '/')->name('bar-chart');
Route::redirect('/alerts', '/')->name('alerts');
Route::redirect('/avatars', '/')->name('avatars');
Route::redirect('/badge', '/')->name('badges');
Route::redirect('/buttons', '/')->name('buttons');
Route::redirect('/image', '/')->name('images');
Route::redirect('/videos', '/')->name('videos');

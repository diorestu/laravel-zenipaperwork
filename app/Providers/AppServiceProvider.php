<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\BankAccount;
use App\Models\BillingSubmission;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\CreditNote;
use App\Policies\CompanyOwnedPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Client::class, CompanyOwnedPolicy::class);
        Gate::policy(BankAccount::class, CompanyOwnedPolicy::class);
        Gate::policy(BillingSubmission::class, CompanyOwnedPolicy::class);
        Gate::policy(Product::class, CompanyOwnedPolicy::class);
        Gate::policy(Invoice::class, CompanyOwnedPolicy::class);
        Gate::policy(Quotation::class, CompanyOwnedPolicy::class);
        Gate::policy(CreditNote::class, CompanyOwnedPolicy::class);

        Invoice::created(function (Invoice $invoice) {
            try {
                app(\App\Services\FirebasePushService::class)->sendToCompany(
                    $invoice->company_id,
                    'Invoice Baru',
                    "Invoice {$invoice->number} telah berhasil dibuat.",
                    ['type' => 'invoice', 'id' => (string)$invoice->id]
                );
            } catch (\Throwable $e) {}
        });

        Invoice::updated(function (Invoice $invoice) {
            if ($invoice->wasChanged('status')) {
                try {
                    $statusMapping = ['draft' => 'Draft', 'sent' => 'Terkirim', 'partial' => 'Dibayar Sebagian', 'paid' => 'Lunas', 'overdue' => 'Jatuh Tempo', 'cancelled' => 'Dibatalkan'];
                    $statusLabel = $statusMapping[$invoice->status] ?? $invoice->status;
                    app(\App\Services\FirebasePushService::class)->sendToCompany(
                        $invoice->company_id,
                        'Status Invoice Berubah',
                        "Invoice {$invoice->number} berstatus {$statusLabel}.",
                        ['type' => 'invoice', 'id' => (string)$invoice->id]
                    );
                } catch (\Throwable $e) {}
            }
        });

        Quotation::created(function (Quotation $quotation) {
            try {
                app(\App\Services\FirebasePushService::class)->sendToCompany(
                    $quotation->company_id,
                    'Penawaran Baru',
                    "Penawaran {$quotation->number} telah berhasil dibuat.",
                    ['type' => 'quotation', 'id' => (string)$quotation->id]
                );
            } catch (\Throwable $e) {}
        });

        \App\Models\Expense::created(function (\App\Models\Expense $expense) {
            try {
                $amount = number_format($expense->amount, 0, ',', '.');
                app(\App\Services\FirebasePushService::class)->sendToCompany(
                    $expense->company_id,
                    'Pengeluaran Baru',
                    "Pengeluaran baru sebesar Rp {$amount} telah dicatat.",
                    ['type' => 'expense', 'id' => (string)$expense->id]
                );
            } catch (\Throwable $e) {}
        });
    }
}

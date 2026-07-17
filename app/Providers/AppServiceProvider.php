<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\BankAccount;
use App\Models\BillingSubmission;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
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
    }
}

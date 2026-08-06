<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tax_number',
        'logo_path',
        'pic_name',
        'pic_email',
        'pic_phone',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'trial_ends_at',
        'active_plan',
        'subscription_ends_at',
        'pakasir_project_id',
        'pakasir_api_key',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function getActivePlanSlug(): ?string
    {
        if ($this->active_plan && $this->subscription_ends_at && $this->subscription_ends_at->isFuture()) {
            return $this->active_plan;
        }
        if ($this->onTrial()) {
            return 'trial';
        }
        return null;
    }

    public function hasReachedClientLimit(): bool
    {
        $plan = $this->getActivePlanSlug();
        $limit = match ($plan) {
            'starter' => 100,
            'business', 'trial' => 500,
            default => -1, // Unlimited for enterprise or others
        };

        if ($limit === -1) {
            return false;
        }

        return $this->clients()->count() >= $limit;
    }

    public function hasReachedProductLimit(): bool
    {
        $plan = $this->getActivePlanSlug();
        $limit = match ($plan) {
            'starter' => 100,
            'business', 'trial' => 500,
            default => -1,
        };

        if ($limit === -1) {
            return false;
        }

        return $this->products()->count() >= $limit;
    }

    public function hasReachedInvoiceLimit(): bool
    {
        $plan = $this->getActivePlanSlug();
        if ($plan === 'starter') {
            $startOfMonth = now()->startOfMonth();
            $invoicesCount = $this->invoices()->where('created_at', '>=', $startOfMonth)->count();
            $quotationsCount = $this->quotations()->where('created_at', '>=', $startOfMonth)->count();
            return ($invoicesCount + $quotationsCount) >= 50;
        }

        if ($plan === 'business' || $plan === 'trial') {
            return $this->invoices()->count() >= 500;
        }

        return false;
    }

    public function hasReachedQuotationLimit(): bool
    {
        $plan = $this->getActivePlanSlug();
        if ($plan === 'starter') {
            $startOfMonth = now()->startOfMonth();
            $invoicesCount = $this->invoices()->where('created_at', '>=', $startOfMonth)->count();
            $quotationsCount = $this->quotations()->where('created_at', '>=', $startOfMonth)->count();
            return ($invoicesCount + $quotationsCount) >= 50;
        }

        return false;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}

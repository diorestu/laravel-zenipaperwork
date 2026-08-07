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
        'invoice_number_prefix',
        'invoice_number_format',
        'invoice_number_padding',
        'invoice_next_number',
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

    public function getActivePlanSlug(): string
    {
        if ($this->active_plan && $this->subscription_ends_at && $this->subscription_ends_at->isFuture()) {
            return $this->active_plan;
        }
        if ($this->onTrial()) {
            return 'trial';
        }

        return 'free';
    }

    public function hasReachedClientLimit(): bool
    {
        $plan = $this->getActivePlanSlug();
        $limit = match ($plan) {
            'free' => 20,
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
            'free' => 20,
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
        if ($plan === 'free') {
            $startOfMonth = now()->startOfMonth();
            $invoicesCount = $this->invoices()->where('created_at', '>=', $startOfMonth)->count();
            $quotationsCount = $this->quotations()->where('created_at', '>=', $startOfMonth)->count();

            return ($invoicesCount + $quotationsCount) >= 25;
        }

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
        if ($plan === 'free') {
            $startOfMonth = now()->startOfMonth();
            $invoicesCount = $this->invoices()->where('created_at', '>=', $startOfMonth)->count();
            $quotationsCount = $this->quotations()->where('created_at', '>=', $startOfMonth)->count();

            return ($invoicesCount + $quotationsCount) >= 25;
        }

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

    public function generateNextInvoiceNumber(?\Carbon\Carbon $date = null, bool $increment = false): string
    {
        $date = $date ?? now();
        $prefix = $this->invoice_number_prefix ?: 'INV';
        $format = $this->invoice_number_format ?: '{PREFIX}/{YYYY}/{MM}/{NUMBER}';
        $padding = max(1, (int) ($this->invoice_number_padding ?: 4));
        $nextNumber = max(1, (int) ($this->invoice_next_number ?: 1));

        $paddedNumber = str_pad((string) $nextNumber, $padding, '0', STR_PAD_LEFT);

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $romanMonth = $romanMonths[$date->month] ?? (string) $date->month;

        $replacements = [
            '{PREFIX}' => $prefix,
            '{YYYY}' => $date->format('Y'),
            '{YY}' => $date->format('y'),
            '{MM}' => $date->format('m'),
            '{DD}' => $date->format('d'),
            '{NUMBER}' => $paddedNumber,
            '{NUM}' => $paddedNumber,
            '{ROMAN}' => $romanMonth,
            '{ROMAN_MONTH}' => $romanMonth,
        ];

        $generatedNumber = str_replace(array_keys($replacements), array_values($replacements), $format);

        if ($increment) {
            $this->increment('invoice_next_number');
        }

        return $generatedNumber;
    }
}

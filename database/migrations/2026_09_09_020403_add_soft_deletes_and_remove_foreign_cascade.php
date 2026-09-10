<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add deleted_at (soft deletes) to all domain tables
        $tables = [
            'companies',
            'users',
            'clients',
            'products',
            'quotations',
            'quotation_items',
            'quotation_payment_terms',
            'invoices',
            'invoice_items',
            'invoice_payments',
            'invoice_payment_terms',
            'credit_notes',
            'invoice_expenses',
            'expenses',
            'bank_accounts',
            'billing_submissions',
            'notifications',
            'user_device_tokens',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }

        // 2. Modify foreign keys from CASCADE to RESTRICT (or NO ACTION) to prevent cascade deletes
        $foreignKeyUpdates = [
            // Bank accounts
            ['table' => 'bank_accounts', 'column' => 'company_id', 'foreign' => 'bank_accounts_company_id_foreign', 'on' => 'companies'],
            // Billing submissions
            ['table' => 'billing_submissions', 'column' => 'company_id', 'foreign' => 'billing_submissions_company_id_foreign', 'on' => 'companies'],
            // Clients
            ['table' => 'clients', 'column' => 'company_id', 'foreign' => 'clients_company_id_foreign', 'on' => 'companies'],
            // Credit notes
            ['table' => 'credit_notes', 'column' => 'company_id', 'foreign' => 'credit_notes_company_id_foreign', 'on' => 'companies'],
            ['table' => 'credit_notes', 'column' => 'invoice_id', 'foreign' => 'credit_notes_invoice_id_foreign', 'on' => 'invoices'],
            ['table' => 'credit_notes', 'column' => 'client_id', 'foreign' => 'credit_notes_client_id_foreign', 'on' => 'clients'],
            // Expenses
            ['table' => 'expenses', 'column' => 'company_id', 'foreign' => 'expenses_company_id_foreign', 'on' => 'companies'],
            // Invoices
            ['table' => 'invoices', 'column' => 'company_id', 'foreign' => 'invoices_company_id_foreign', 'on' => 'companies'],
            ['table' => 'invoices', 'column' => 'client_id', 'foreign' => 'invoices_client_id_foreign', 'on' => 'clients'],
            // Invoice expenses
            ['table' => 'invoice_expenses', 'column' => 'company_id', 'foreign' => 'invoice_expenses_company_id_foreign', 'on' => 'companies'],
            ['table' => 'invoice_expenses', 'column' => 'invoice_id', 'foreign' => 'invoice_expenses_invoice_id_foreign', 'on' => 'invoices'],
            // Invoice items
            ['table' => 'invoice_items', 'column' => 'invoice_id', 'foreign' => 'invoice_items_invoice_id_foreign', 'on' => 'invoices'],
            // Invoice payments
            ['table' => 'invoice_payments', 'column' => 'invoice_id', 'foreign' => 'invoice_payments_invoice_id_foreign', 'on' => 'invoices'],
            // Invoice payment terms
            ['table' => 'invoice_payment_terms', 'column' => 'invoice_id', 'foreign' => 'invoice_payment_terms_invoice_id_foreign', 'on' => 'invoices'],
            // Notifications
            ['table' => 'notifications', 'column' => 'company_id', 'foreign' => 'notifications_company_id_foreign', 'on' => 'companies'],
            ['table' => 'notifications', 'column' => 'user_id', 'foreign' => 'notifications_user_id_foreign', 'on' => 'users'],
            // Products
            ['table' => 'products', 'column' => 'company_id', 'foreign' => 'products_company_id_foreign', 'on' => 'companies'],
            // Quotations
            ['table' => 'quotations', 'column' => 'company_id', 'foreign' => 'quotations_company_id_foreign', 'on' => 'companies'],
            ['table' => 'quotations', 'column' => 'client_id', 'foreign' => 'quotations_client_id_foreign', 'on' => 'clients'],
            // Quotation items
            ['table' => 'quotation_items', 'column' => 'quotation_id', 'foreign' => 'quotation_items_quotation_id_foreign', 'on' => 'quotations'],
            // Quotation payment terms
            ['table' => 'quotation_payment_terms', 'column' => 'quotation_id', 'foreign' => 'quotation_payment_terms_quotation_id_foreign', 'on' => 'quotations'],
            // User device tokens
            ['table' => 'user_device_tokens', 'column' => 'company_id', 'foreign' => 'user_device_tokens_company_id_foreign', 'on' => 'companies'],
            ['table' => 'user_device_tokens', 'column' => 'user_id', 'foreign' => 'user_device_tokens_user_id_foreign', 'on' => 'users'],
        ];

        if (DB::getDriverName() !== 'sqlite') {
            foreach ($foreignKeyUpdates as $fk) {
                if (Schema::hasTable($fk['table']) && Schema::hasColumn($fk['table'], $fk['column'])) {
                    Schema::table($fk['table'], function (Blueprint $table) use ($fk) {
                        try {
                            $table->dropForeign($fk['foreign']);
                        } catch (\Throwable $e) {
                            // ignore if FK does not exist
                        }
                        $table->foreign($fk['column'])
                            ->references('id')
                            ->on($fk['on'])
                            ->restrictOnDelete();
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'companies',
            'users',
            'clients',
            'products',
            'quotations',
            'quotation_items',
            'quotation_payment_terms',
            'invoices',
            'invoice_items',
            'invoice_payments',
            'invoice_payment_terms',
            'credit_notes',
            'invoice_expenses',
            'expenses',
            'bank_accounts',
            'billing_submissions',
            'notifications',
            'user_device_tokens',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};

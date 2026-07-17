<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('tax_number');
            $table->string('pic_name')->nullable()->after('logo_path');
            $table->string('pic_email')->nullable()->after('pic_name');
            $table->string('pic_phone')->nullable()->after('pic_email');
        });

        Schema::table('billing_submissions', function (Blueprint $table) {
            $table->string('payment_gateway')->nullable()->after('payment_method');
            $table->string('payment_order_id')->nullable()->after('payment_gateway');
            $table->string('payment_number')->nullable()->after('payment_order_id');
            $table->string('payment_url')->nullable()->after('payment_number');
            $table->json('payment_payload')->nullable()->after('payment_url');
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('branch')->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'bank_name']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');

        Schema::table('billing_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_gateway',
                'payment_order_id',
                'payment_number',
                'payment_url',
                'payment_payload',
            ]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'pic_name', 'pic_email', 'pic_phone']);
        });
    }
};

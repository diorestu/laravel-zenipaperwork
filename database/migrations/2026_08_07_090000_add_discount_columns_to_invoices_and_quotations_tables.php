<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->string('discount_type', 20)->default('fixed')->after('pph_amount');
            $table->decimal('discount_rate', 8, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_rate');
        });

        Schema::table('quotations', function (Blueprint $table): void {
            $table->string('discount_type', 20)->default('fixed')->after('tax_total');
            $table->decimal('discount_rate', 8, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_rate');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropColumn(['discount_type', 'discount_rate', 'discount_amount']);
        });

        Schema::table('quotations', function (Blueprint $table): void {
            $table->dropColumn(['discount_type', 'discount_rate', 'discount_amount']);
        });
    }
};

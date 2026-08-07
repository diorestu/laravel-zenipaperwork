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
        Schema::table('invoices', function (Blueprint $table) {
            $table->json('custom_taxes')->nullable()->after('pph_amount');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->json('custom_taxes')->nullable()->after('tax_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('custom_taxes');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('custom_taxes');
        });
    }
};

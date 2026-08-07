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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('invoice_number_prefix')->default('INV')->after('pakasir_api_key');
            $table->string('invoice_number_format')->default('{PREFIX}/{YYYY}/{MM}/{NUMBER}')->after('invoice_number_prefix');
            $table->unsignedInteger('invoice_number_padding')->default(4)->after('invoice_number_format');
            $table->unsignedInteger('invoice_next_number')->default(1)->after('invoice_number_padding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number_prefix',
                'invoice_number_format',
                'invoice_number_padding',
                'invoice_next_number',
            ]);
        });
    }
};

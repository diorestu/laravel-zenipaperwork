<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('pakasir_project_id')->nullable();
            $table->string('pakasir_api_key')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_order_id')->nullable()->unique();
            $table->string('payment_number')->nullable();
            $table->string('payment_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['pakasir_project_id', 'pakasir_api_key']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_order_id', 'payment_number', 'payment_url']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table): void {
            $table->unsignedTinyInteger('term_number')->nullable()->after('invoice_id');
            $table->string('term_label')->nullable()->after('term_number');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table): void {
            $table->dropColumn(['term_number', 'term_label']);
        });
    }
};

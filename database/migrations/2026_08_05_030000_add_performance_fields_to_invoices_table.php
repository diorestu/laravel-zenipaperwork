<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount_paid', 15, 2)->default(0)->after('total');
            $table->decimal('credit_note_total', 15, 2)->default(0)->after('amount_paid');
            $table->decimal('balance_due', 15, 2)->default(0)->after('credit_note_total');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'credit_note_total', 'balance_due']);
        });
    }
};

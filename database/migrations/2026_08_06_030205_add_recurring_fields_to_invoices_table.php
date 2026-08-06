<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_cycle')->nullable();
            $table->date('next_recurrence_date')->nullable();
            $table->foreignId('parent_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['parent_invoice_id']);
            $table->dropColumn(['is_recurring', 'recurring_cycle', 'next_recurrence_date', 'parent_invoice_id']);
        });
    }
};

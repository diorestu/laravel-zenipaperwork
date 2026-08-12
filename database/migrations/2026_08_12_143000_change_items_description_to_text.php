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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->text('description')->change();
        });

        if (Schema::hasTable('credit_note_items')) {
            Schema::table('credit_note_items', function (Blueprint $table) {
                $table->text('description')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('description')->change();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('description')->change();
        });

        if (Schema::hasTable('credit_note_items')) {
            Schema::table('credit_note_items', function (Blueprint $table) {
                $table->string('description')->change();
            });
        }
    }
};

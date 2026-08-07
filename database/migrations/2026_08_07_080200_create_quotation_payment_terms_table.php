<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_payment_terms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('term_number');
            $table->string('label');
            $table->decimal('amount', 15, 2);
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->unique(['quotation_id', 'term_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_payment_terms');
    }
};

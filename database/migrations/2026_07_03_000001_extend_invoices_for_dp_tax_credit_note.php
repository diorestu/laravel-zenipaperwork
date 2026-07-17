<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->decimal('down_payment_amount', 15, 2)->default(0)->after('total');
            $table->decimal('pph_rate', 8, 2)->default(0)->after('tax_total');
            $table->decimal('pph_amount', 15, 2)->default(0)->after('pph_rate');
            $table->decimal('expense_total', 15, 2)->default(0)->after('pph_amount');
            $table->decimal('profit_total', 15, 2)->default(0)->after('expense_total');
            $table->timestamp('sent_at')->nullable()->after('due_date');
            $table->timestamp('last_reminder_at')->nullable()->after('sent_at');
        });

        Schema::create('credit_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->date('issue_date');
            $table->decimal('amount', 15, 2);
            $table->string('reason')->nullable();
            $table->string('status')->default('applied');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'number']);
            $table->index('invoice_id');
        });

        Schema::create('invoice_expenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('category')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_expenses');
        Schema::dropIfExists('credit_notes');

        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropColumn([
                'down_payment_amount',
                'pph_rate',
                'pph_amount',
                'expense_total',
                'profit_total',
                'sent_at',
                'last_reminder_at',
            ]);
        });
    }
};

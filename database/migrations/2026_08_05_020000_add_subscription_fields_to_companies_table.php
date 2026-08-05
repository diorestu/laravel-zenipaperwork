<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('active_plan')->nullable()->after('trial_ends_at');
            $table->timestamp('subscription_ends_at')->nullable()->after('active_plan');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['active_plan', 'subscription_ends_at']);
        });
    }
};

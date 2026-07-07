<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_companies', function (Blueprint $table) {
            $table->boolean('fixed_service_charge')->default(false)->after('status');
            $table->decimal('fixed_service_charge_amount', 12, 2)->nullable()->after('fixed_service_charge');
        });
    }

    public function down(): void
    {
        Schema::table('finance_companies', function (Blueprint $table) {
            $table->dropColumn(['fixed_service_charge', 'fixed_service_charge_amount']);
        });
    }
};

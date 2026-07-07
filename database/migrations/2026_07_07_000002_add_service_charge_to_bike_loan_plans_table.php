<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bike_loan_plans', function (Blueprint $table) {
            // The actual service charge Rs value used when this plan was saved
            // (either the finance company's fixed amount, or percent x loan_amount).
            $table->decimal('service_charge', 12, 2)->nullable()->after('rmv');
            // The percentage used, only when the finance company was NOT on a
            // fixed service charge. Null when a fixed amount was used instead.
            $table->decimal('service_charge_percent', 5, 2)->nullable()->after('service_charge');
        });
    }

    public function down(): void
    {
        Schema::table('bike_loan_plans', function (Blueprint $table) {
            $table->dropColumn(['service_charge', 'service_charge_percent']);
        });
    }
};

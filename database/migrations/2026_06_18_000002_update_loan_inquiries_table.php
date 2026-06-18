<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_inquiries', function (Blueprint $table) {
            $table->decimal('loan_amount', 12, 2)->nullable()->after('bike_price');
            $table->decimal('bike_dp', 12, 2)->nullable()->after('loan_amount');
            $table->decimal('service_charge', 10, 2)->nullable()->after('bike_dp');
            $table->decimal('rmv', 10, 2)->nullable()->after('service_charge');
            $table->decimal('minimum_dp', 12, 2)->nullable()->after('rmv');
            $table->decimal('interest_rate', 5, 2)->nullable()->after('minimum_dp');
            $table->decimal('monthly_payment', 12, 2)->nullable()->after('interest_rate');
            // loan_term_months දැනටමත් තිබෙනවා ✅
        });
    }

    public function down(): void
    {
        Schema::table('loan_inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'loan_amount', 'bike_dp', 'service_charge',
                'rmv', 'minimum_dp', 'interest_rate', 'monthly_payment'
            ]);
        });
    }
};
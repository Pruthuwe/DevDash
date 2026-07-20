<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_inquiries', function (Blueprint $table) {
            // Was being sent by the frontend but never had a column to land
            // in — silently dropped. Added so staff can see which company
            // the customer picked.
            $table->string('finance_company')->nullable()->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('loan_inquiries', function (Blueprint $table) {
            $table->dropColumn('finance_company');
        });
    }
};

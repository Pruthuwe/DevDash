<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Minimum down payment percentage, settable per Category (e.g.
     * "Motorcycles") and overridable per Brand (a sub-category, e.g.
     * "Bajaj"). The Loan Calculator uses the brand's value if set,
     * otherwise falls back to its parent category's value.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('min_down_payment_percent', 5, 2)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('min_down_payment_percent');
        });
    }
};

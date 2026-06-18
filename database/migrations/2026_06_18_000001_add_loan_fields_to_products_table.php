

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('loan_amount', 12, 2)->nullable()->after('sale_price');
            $table->decimal('rmv', 10, 2)->default(10160)->after('loan_amount');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['loan_amount', 'rmv']);
        });
    }
};
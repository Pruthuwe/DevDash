<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bike_loan_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('finance_company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->decimal('loan_amount', 12, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->default(1.5);
            $table->decimal('rmv', 10, 2)->default(10160);
            $table->timestamps();

            // A bike can only have ONE plan per finance company
            $table->unique(['product_id', 'finance_company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bike_loan_plans');
    }
};

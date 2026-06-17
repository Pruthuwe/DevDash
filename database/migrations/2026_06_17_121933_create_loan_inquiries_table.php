<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('phone');
            $table->string('city')->nullable();
            $table->decimal('bike_price', 12, 2)->nullable();
            $table->decimal('down_payment', 12, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->unsignedInteger('loan_term_months')->nullable();
            $table->decimal('monthly_payment', 12, 2)->nullable();
            $table->enum('status', ['new', 'contacted', 'closed'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_inquiries');
    }
};
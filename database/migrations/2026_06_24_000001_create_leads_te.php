<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // ── Basic Info ──────────────────────────────────────────
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();

            // ── Business Info ───────────────────────────────────────
            // Note: 'Garage' removed — not a real customer type for this business.
            $table->enum('customer_type', ['Individual', 'Dealer', 'Other'])->default('Individual');
            $table->string('company_name')->nullable();

            // ── Interest Details ─────────────────────────────────────
            // Vehicle Brand = a subcategory row in `categories` (e.g. KTM, Bajaj)
            // — same table the product catalog already uses for "Brand".
            $table->foreignId('vehicle_brand_id')->nullable()->constrained('categories')->nullOnDelete();

            // Vehicle Model — there's no dedicated "model" table; models are
            // just product names. We store the matched product's id when the
            // salesperson picks an existing model, plus the raw text (so a
            // lead can still reference a model that isn't in stock yet).
            $table->foreignId('vehicle_model_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('vehicle_model_text')->nullable();

            $table->decimal('budget_range', 12, 2)->nullable();
            $table->integer('quantity_needed')->nullable()->default(1);

            // ── Source ──────────────────────────────────────────────
            $table->enum('lead_source', ['Walk-in', 'Web Enquiry', 'Social Media', 'Phone Call', 'Other'])
                  ->default('Walk-in');

            // ── Assignment & Status ─────────────────────────────────
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Unassigned', 'Interested', 'Need More Info', 'Follow Up Later', 'Not Interested', 'Converted', 'Closed'])
                  ->default('Unassigned');

            // ── Priority (web source = highest) ─────────────────────
            $table->enum('priority', ['High', 'Normal', 'Low'])->default('Normal');

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

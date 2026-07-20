<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_followups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('done_by')->constrained('users')->cascadeOnDelete();

            $table->dateTime('followup_at');
            $table->enum('method', ['Call', 'Visit', 'WhatsApp', 'Email', 'Other'])->default('Call');
            $table->text('feedback')->nullable();
            $table->enum('status', ['Interested', 'Need More Info', 'Follow Up Later', 'Not Interested', 'Converted', 'Closed'])
                  ->default('Interested');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_followups');
    }
};

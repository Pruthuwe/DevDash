<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Drop company_name column
            $table->dropColumn('company_name');
            
            // Drop priority column
            $table->dropColumn('priority');
            
            // Add next_followup_at for dashboard reminders
            $table->dateTime('next_followup_at')->nullable()->after('status');
        });

        // Update status enum: replace 'Closed' with 'Sales Done'
        // Note: MySQL enum modification - adjust if using different DB
        DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('Unassigned','Interested','Need More Info','Follow Up Later','Not Interested','Converted','Sales Done') DEFAULT 'Unassigned'");
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('customer_type');
            $table->enum('priority', ['High', 'Normal', 'Low'])->default('Normal')->after('lead_source');
            $table->dropColumn('next_followup_at');
        });

        DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('Unassigned','Interested','Need More Info','Follow Up Later','Not Interested','Converted','Closed') DEFAULT 'Unassigned'");
    }
};
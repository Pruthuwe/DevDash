<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update status enum: remove 'Closed', add 'Sales Done'
        DB::statement("ALTER TABLE lead_followups MODIFY COLUMN status ENUM('Interested','Need More Info','Follow Up Later','Not Interested','Converted','Sales Done') DEFAULT 'Interested'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lead_followups MODIFY COLUMN status ENUM('Interested','Need More Info','Follow Up Later','Not Interested','Converted','Closed') DEFAULT 'Interested'");
    }
};
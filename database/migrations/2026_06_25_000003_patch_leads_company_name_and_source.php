<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Brings `leads` in line with what the app code actually needs, on top
     * of the 3 migrations already run manually (which added
     * lead_followups.next_followup_at, dropped leads.company_name +
     * leads.priority, added leads.next_followup_at, and renamed the
     * 'Closed' status to 'Sales Done' on both tables).
     *
     * This migration:
     *   - Re-adds company_name (it's still actively used — the drop was a
     *     mistake, not a deliberate field removal).
     *   - Adds source_type / source_id so a Lead auto-converted from a
     *     LoanInquiry or ProductEnquiry can be found again instead of
     *     duplicated when that enquiry is contacted more than once.
     *
     * It does NOT touch lead_followups.next_followup_at, leads.priority,
     * or the Closed -> Sales Done rename — those are already done.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'company_name')) {
                $table->string('company_name')->nullable()->after('customer_type');
            }

            if (!Schema::hasColumn('leads', 'source_type')) {
                $table->string('source_type')->nullable()->after('lead_source');
            }
            if (!Schema::hasColumn('leads', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->index(['source_type', 'source_id'], 'leads_source_type_source_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_source_type_source_id_index');
            $table->dropColumn(['source_type', 'source_id']);
            $table->dropColumn('company_name');
        });
    }
};

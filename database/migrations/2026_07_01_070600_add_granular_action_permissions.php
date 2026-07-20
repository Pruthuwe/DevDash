<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use Database\Seeders\PermissionSeeder;

return new class extends Migration
{
    /**
     * Backfills every permission in PermissionSeeder::moduleMap() that
     * doesn't already exist yet — in particular the new "finance-companies"
     * and "loan-plans" modules, and the extra "assign / export / import"
     * actions for leads, appointments and users. Safe to run on an
     * existing database; uses firstOrCreate so nothing is duplicated.
     */
    public function up(): void
    {
        foreach (PermissionSeeder::moduleMap() as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => $action . '-' . $module]);
            }
        }
    }

    public function down(): void
    {
        // Intentionally left blank — we never want to silently delete
        // permissions that may already be attached to live roles.
    }
};

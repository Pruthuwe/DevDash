<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use Database\Seeders\PermissionSeeder;

return new class extends Migration
{
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
        // Intentionally left blank — never delete permissions that may
        // already be attached to live roles.
    }
};
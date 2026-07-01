<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Every module in the system, mapped to the actions that actually exist
     * for it (not just the standard view/create/edit/delete four).
     *
     * Keep this list in sync with routes/web.php — if a new route gets a
     * ->middleware('permission:...') added, add the matching action here
     * too so it shows up as a checkbox on Add/Edit Role.
     */
    public static function moduleMap(): array
    {
        return [
            'categories'         => ['view', 'create', 'edit', 'delete'],
            'products'           => ['view', 'create', 'edit', 'delete'],
            'customers'          => ['view', 'create', 'edit', 'delete'],
            'suppliers'          => ['view', 'create', 'edit', 'delete'],
            'purchases'          => ['view', 'create', 'edit', 'delete'],
            'quotations'         => ['view', 'create', 'edit', 'delete'],
            'blogs'              => ['view', 'create', 'edit', 'delete'],
            'services'           => ['view', 'create', 'edit', 'delete'],
            'appointments'       => ['view', 'create', 'edit', 'delete', 'import'],
            'contacts'           => ['view', 'edit', 'delete'],
            'loan-inquiries'     => ['view', 'edit', 'delete'],
            'product-enquiries'  => ['view', 'edit', 'delete'],
            'leads'              => ['view', 'create', 'edit', 'delete', 'assign', 'export', 'import'],
            'finance-companies'  => ['view', 'create', 'edit', 'delete'],
            'loan-plans'         => ['view', 'create', 'delete'],
            'users'              => ['view', 'create', 'edit', 'delete', 'assign'],
            'roles'              => ['view', 'create', 'edit', 'delete'],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::moduleMap() as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => $action . '-' . $module]);
            }
        }
    }
}

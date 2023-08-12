<?php

use App\Models\System\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        $roles = [
            'admin-system',
            'admin-warehouse',
            'admin-partner',
            'user-driver',
            'user-customer',
            'user-sap',
            'user-normal'
        ];

         foreach ($roles as $role) {
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
            }
        }
    }
}

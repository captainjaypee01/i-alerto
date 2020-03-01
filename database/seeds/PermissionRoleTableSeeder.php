<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{

    use DisableForeignKeys;

    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Create Roles
        $admin = Role::create(['name' => 'administrator', 'guard_name' => 'backpack']);
        $user = Role::create(['name' => 'user', 'guard_name' => 'backpack']);
        $employee = Role::create(['name' => 'employee', 'guard_name' => 'backpack']);

        // Create Permissions
        $permissions = ['manage user', 'manage announcement', 'manage alert', 'manage report'];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'backpack']);
        }

        // ALWAYS GIVE ADMIN ROLE ALL PERMISSIONS
        $admin->givePermissionTo(Permission::all());
        $employee->givePermissionTo(['manage announcement', 'manage report', 'manage alert']);
        $user->givePermissionTo(['manage alert']);
 
        $this->enableForeignKeys();
    }
}

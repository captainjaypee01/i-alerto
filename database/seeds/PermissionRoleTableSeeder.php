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
        $employee = Role::create(['name' => 'employee', 'guard_name' => 'backpack']);
        $resident = Role::create(['name' => 'resident', 'guard_name' => 'backpack']);
        $official = Role::create(['name' => 'official', 'guard_name' => 'backpack']);
        $relative = Role::create(['name' => 'relative', 'guard_name' => 'backpack']);

        // Create Permissions
        $permissions = ['manage user', 'manage announcement', 'manage alert', 'manage report', 'manage permission', 'manage evacuation','manage role', 'manage resident', 'manage barangay', 'manage relative', 'manage employee', 'manage official'];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'backpack']);
        }

        // ALWAYS GIVE ADMIN ROLE ALL PERMISSIONS
        $admin->givePermissionTo(Permission::all());
        $employee->givePermissionTo(['manage announcement', 'manage report', 'manage alert', 'manage employee', 'manage evacuation', 'manage resident', 'manage official']);
        $resident->givePermissionTo(['manage alert']);
        $official->givePermissionTo(['manage evacuation', 'manage report','manage alert']);

        $this->enableForeignKeys();
    }
}

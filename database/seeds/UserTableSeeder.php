<?php

use App\Models\BackpackUser as User;
use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder.
 */
class UserTableSeeder extends Seeder
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

        // Add the master administrator, user id of 1
        User::create([
            'name'              => 'Admin Istrator',
            'email'             => 'admin@admin.com',
            'password'          => Hash::make('secret'),
        ]);
        
        User::create([
            'name'              => 'Employee Employee',
            'email'             => 'employee@employee.com',
            'password'          => Hash::make('secret'),
        ]);

        User::create([
            'name'              => 'Default User',
            'email'             => 'user@user.com',
            'password'          => Hash::make('secret'),
        ]);

        $this->enableForeignKeys();
    }
}

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
            'first_name'              => 'Admin Istrator',
            'middle_name'              => 'A',
            'last_name'              => 'Istrator',
            'email'             => 'admin@admin.com',
            'contact_number'    => '09123456789',
            'password'          => Hash::make('secret'),
            'address'           => 'Sample Address',
        ]);

        User::create([
            'first_name'              => 'One',
            'middle_name'              => 'E',
            'last_name'              => 'Employee',
            'email'             => 'one@employee.com',
            'contact_number'    => '09123321788',
            'password'          => Hash::make('secret'),
            'address'           => 'Sample Address',
        ]);

        User::create([
            'first_name'              => 'One',
            'middle_name'              => 'R',
            'last_name'              => 'Resident',
            'email'             => 'one@resident.com',
            'contact_number'    => '09123450987',
            'password'          => Hash::make('secret'),
            'address'           => 'Sample Address',
        ]);

        User::create([
            'first_name'              => 'One',
            'middle_name'              => 'b',
            'last_name'              => 'Barangay',
            'email'             => 'one@barangay.com',
            'contact_number'    => '09156756787',
            'password'          => Hash::make('secret'),
            'address'           => 'Sample Address',
        ]);

        $this->enableForeignKeys();
    }
}

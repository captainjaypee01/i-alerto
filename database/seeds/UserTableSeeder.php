<?php

use App\Models\BackpackUser as User;
use App\Models\Employee;
use App\Models\Official;
use App\Models\Resident;
use Carbon\Carbon;
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
            'birthdate'         => Carbon::createFromTimestampMs(906307200000)->format('Y-m-d'),
            'email_verified_at' => now(),
            'password'          => Hash::make('secret'),
        ]);

        User::create([
            'first_name'              => 'One',
            'middle_name'              => 'E',
            'last_name'              => 'Employee',
            'email'             => 'one@employee.com',
            'contact_number'    => '09123321788',
            'birthdate'         => Carbon::createFromTimestampMs(906307200000)->format('Y-m-d'),
            'email_verified_at' => now(),
            'password'          => Hash::make('secret'),
        ]);

        User::create([
            'first_name'              => 'One',
            'middle_name'              => 'R',
            'last_name'              => 'Resident',
            'email'             => 'one@resident.com',
            'contact_number'    => '09123450987',
            'birthdate'         => Carbon::createFromTimestampMs(906307200000)->format('Y-m-d'),
            'email_verified_at' => now(),
            'password'          => Hash::make('secret'),
        ]);

        User::create([
            'first_name'              => 'One',
            'middle_name'              => 'b',
            'last_name'              => 'Barangay',
            'email'             => 'one@official.com',
            'contact_number'    => '09156756787',
            'birthdate'         => Carbon::createFromTimestampMs(906307200000)->format('Y-m-d'),
            'email_verified_at' => now(),
            'password'          => Hash::make('secret'),
        ]);

        Employee::create([
            'user_id'           => 2,
            'barangay_id'       => 1,
            'first_name'              => 'One',
            'middle_name'              => 'E',
            'last_name'              => 'Employee',
            'email'             => 'one@employee.com',
            'contact_number'    => '09123321788',
            'birthdate'         => Carbon::createFromTimestampMs(906307200000)->format('Y-m-d'),
        ]);

        Official::create([
            'user_id'           => 4,
            'barangay_id'       => 1,
            'first_name'              => 'One',
            'middle_name'              => 'E',
            'last_name'              => 'Official',
            'email'             => 'one@official.com',
            'contact_number'    => '09378473123',
            'birthdate'         => Carbon::createFromTimestampMs(906307200000)->format('Y-m-d'),
        ]);

        Resident::create([
            'user_id'           => 3,
            'barangay_id'       => 1,
            'first_name'              => 'One',
            'middle_name'              => 'E',
            'last_name'              => 'Resident',
            'email'             => 'one@resident.com',
            'contact_number'    => '092313132',
            'birthdate'         => Carbon::createFromTimestampMs(906307200000)->format('Y-m-d'),
        ]);
        $this->enableForeignKeys();
    }
}

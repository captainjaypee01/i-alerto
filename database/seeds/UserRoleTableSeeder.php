<?php

use App\Models\BackpackUser;
use Illuminate\Database\Seeder;

/**
 * Class UserRoleTableSeeder.
 */
class UserRoleTableSeeder extends Seeder
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

        BackpackUser::find(1)->assignRole('administrator');
        BackpackUser::find(2)->assignRole('employee');
        BackpackUser::find(3)->assignRole('user');

        $this->enableForeignKeys();
    }
}

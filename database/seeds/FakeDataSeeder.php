<?php

use App\Models\Alert;
use App\Models\Announcement;
use App\Models\BackpackUser;
use App\Models\Barangay;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Announcement::class, 30)->create();
        factory(BackpackUser::class, 20)->states('employee')->create();
        factory(BackpackUser::class, 30)->states('official')->create();
        factory(BackpackUser::class, 200)->states('resident')->create();
        // factory(Barangay::class, 24)->create();
    }
}

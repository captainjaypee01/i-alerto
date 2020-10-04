<?php

use App\Models\Alert;
use App\Models\Announcement;
use App\Models\BackpackUser;
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
        factory(BackpackUser::class, 30)->states('employee')->create();
        factory(BackpackUser::class, 600)->states('user')->create();
    }
}

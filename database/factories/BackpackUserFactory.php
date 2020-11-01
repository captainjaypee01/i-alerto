<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Alert;
use App\Models\BackpackUser;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(BackpackUser::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'contact_number' => $faker->unique()->phoneNumber,
        'created_at' => $faker->dateTimeBetween($startDate = '-1 year', $endDate = 'now', $timezone = 'Asia/Manila'),
    ];
});


$factory
    ->state(BackpackUser::class, 'employee', [])
    ->afterCreatingState(BackpackUser::class, 'employee', function ($user, $faker) {
        $user->assignRole('employee');
        
    });

$factory
    ->state(BackpackUser::class, 'user', [])
    ->afterCreatingState(BackpackUser::class, 'user', function ($user, $faker) {
        $user->assignRole('user');
        
        factory(Alert::class, $faker->numberBetween(12,24))->create([
            'user_id' => $user->id,
        ]);
    });
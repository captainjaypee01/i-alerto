<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Alert;
use App\Models\BackpackUser;
use App\Models\Barangay;
use App\Models\Employee;
use App\Models\Official;
use App\Models\Resident;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(BackpackUser::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'middle_name' => $faker->randomLetter,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'province' => "Metro Manila",
        'city' => "Pasig",
        'barangay' => "Bagong Ilog",
        'detailed_address' => "sample address" ,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'contact_number' => $faker->unique()->phoneNumber,
        'birthdate' => $faker->date('Y-m-d', '-10 years'),
        'created_at' => $faker->dateTimeBetween($startDate = '-1 year', $endDate = 'now', $timezone = 'Asia/Manila'),
    ];
});


$factory
    ->state(BackpackUser::class, 'employee', [])
    ->afterCreatingState(BackpackUser::class, 'employee', function ($user, $faker) {
        $user->assignRole('employee');

        factory(Employee::class)->create([
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'birthdate' => $user->birthdate,
            'address' => $user->address,
        ]);
    });

$factory
    ->state(BackpackUser::class, 'official', [])
    ->afterCreatingState(BackpackUser::class, 'official', function ($user, $faker) {
        $user->assignRole('official');

        factory(Official::class)->create([
            'user_id' => $user->id,
            'barangay_id' => $faker->numberBetween(1,20),
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'birthdate' => $user->birthdate,
            'address' => $user->address,
        ]);
        
    });

$factory
    ->state(BackpackUser::class, 'resident', [])
    ->afterCreatingState(BackpackUser::class, 'resident', function ($user, $faker) {
        $user->assignRole('resident');

        factory(Resident::class)->create([
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'birthdate' => $user->birthdate,
            'address' => $user->address,
        ]);

        factory(Alert::class, $faker->numberBetween(12,24))->create([
            'user_id' => $user->id,
        ]);
    });

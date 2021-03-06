<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Alert;
use App\Models\BackpackUser;
use Faker\Generator as Faker;

$factory->define(Alert::class, function (Faker $faker) {
    $types = array('fire', 'flood', 'accident', 'crime', 'others');
    $accidentTypes = ['car','road','fatal','others'];
    $type = $faker->randomElement($types);
    return [
        'user_id' => BackpackUser::all()->random()->id,
        'address' => $faker->address,
        'latitude' => $faker->latitude(14.444444, 14.7777777),
        'longitude' => $faker->longitude(120.0968037, 121.099107),
        'status' => $faker->randomElement([0,1]),
        'type' => $type,
        'accident_type' => $type == 'accident' ? $faker->randomElement($accidentTypes) : null,
        'created_at' => $faker->dateTimeBetween($startDate = '-1 year', $endDate = 'now', $timezone = 'Asia/Manila'),
    ];
});

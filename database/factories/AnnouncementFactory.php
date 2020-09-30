<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Announcement;
use Faker\Generator as Faker;

$factory->define(Announcement::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'details' => $faker->sentences(3, true),
        'created_at' => $faker->dateTimeBetween($startDate = '-1 year', $endDate = 'now', $timezone = 'Asia/Manila'),
    ];
});

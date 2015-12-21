<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'username' => str_replace('.', '_', $faker->unique()->userName),
        'email' => $faker->unique()->email,
        'phone' => rand(1, 1000) . '-' . rand(1, 1000) . ' ' . rand(1, 10000),
        'password' => bcrypt(str_random(10)),
		'phone' => rand(0,2) == 0 ? $faker->phoneNumber : null,
//		'enroller_id' => rand(0,2) == 1 ? rand(1, $total) : null,
		'ip_address' => $faker->ipv4,
        'created_at' => $faker->dateTimeThisYear('now'),
        'updated_at' => $faker->dateTimeThisYear('now')
    ];
});

$factory->define(App\Models\MarketingLink::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'image' => $faker->imageUrl(640, 480),
        'link' => $faker->url,
        'created_at' => $faker->dateTimeThisYear('now'),
        'updated_at' => $faker->dateTimeThisYear('now')
    ];
});

$factory->define(App\Models\Provider::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'image' => 'do-not-delete-broker-seed.png',
        'us_traders' => $faker->boolean($chanceOfGettingTrue = 70),
        'min_deposit' => $faker->numberBetween(100, 999),
        'review' => $faker->text(1000),
        'web_site' => $faker->url(),
        'created_at' => $faker->dateTimeThisYear('now'),
        'updated_at' => $faker->dateTimeThisYear('now')
    ];
});

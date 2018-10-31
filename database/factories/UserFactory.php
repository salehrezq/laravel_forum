<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
  |--------------------------------------------------------------------------
  | Model Factories
  |--------------------------------------------------------------------------
  |
  | This directory should contain each of the model factory definitions for
  | your application. Factories provide a convenient way to generate new
  | model instances for testing / seeding your application's database.
  |
 */

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'username' => $faker->unique()->username(),
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'confirmation_hash' => str_random(60),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Channel::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'slug' => $faker->word,
    ];
});

$factory->define(App\Thread::class, function (Faker $faker) {
    return [
        'user_id' => mt_rand(1, 20),
        'title' => $faker->sentence,
        'body' => $faker->paragraph,
        'channel_id' => mt_rand(1, 10),
    ];
});

$factory->define(App\Reply::class, function (Faker $faker) {
    return [
        'user_id' => mt_rand(1, 20),
        'thread_id' => mt_rand(1, 200),
        'body' => $faker->paragraph,
    ];
});

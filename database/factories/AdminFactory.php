<?php

use Faker\Generator as Faker;

$factory->define(\Demo\Model\Admin::class, function (Faker $faker) {
    static $name;
    static $password;
    static $email;
    static $status;
    return [
        'username' => $name ?? $faker->firstName,
        'email' => $email ?? $faker->email,
        'password' => $password ?? '123456',
        'status' => $status ?? \Demo\Model\Admin::STATUS_ON,
    ];
});

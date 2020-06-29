<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Tenant;
use Faker\Generator as Faker;

$factory->define(Tenant::class, function (Faker $faker) {
    return [
        'key' => 'dummy',
        'clientKey' => 'dummy',
        'oauthClientId' => 'dummy',
        'sharedSecret' => 'dummy',
        'baseUrl' => 'dummy',
        'productType' => 'dummy',
        'description' => 'dummy',
        'eventType' => 'dummy',
        'oauthClientId' => 'dummy',
        'sharedSecret' => 'dummy',
    ];
});

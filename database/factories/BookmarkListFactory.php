<?php

use Faker\Generator as Faker;

$factory->define(App\BookmarkList::class, function (Faker $faker) {
    $user_id = random_int(1,6);
    return [
        'name' => $faker->word." ".$faker->word,
        'user_id' => $user_id,
        'isVisible' => random_int(0,1),
    ];
});

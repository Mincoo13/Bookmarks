<?php

use App\Bookmark;
use Faker\Generator as Faker;

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

$factory->define(App\Comment::class, function (Faker $faker) {
    $user_id = random_int(1,6);
    $bookmarks = Bookmark::where('user_id',$user_id)->get();
    $ids = array();
    foreach ($bookmarks as $bookmark){
        $id = $bookmark->id;
        array_push($ids,$id);
    }
    return [
        'user_id' => random_int(1,6),
        'bookmark_id' => array_random($ids),
        'text' => $faker->text(),

    ];
});
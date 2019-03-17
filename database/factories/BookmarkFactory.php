<?php

use App\Category;
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

$factory->define(App\Bookmark::class, function (Faker $faker) {
    $user_id = random_int(1,6);
    $categories = Category::where('user_id',$user_id)->get();
    $ids = array();
    foreach ($categories as $category){
        $id = $category->id;
        array_push($ids,$id);
    }
    return [
        'name' => $faker->word." ".$faker->word,
        'url' => $faker->url,
        'description' => $faker->text,
        'user_id' => $user_id,
        'category_id' => array_random($ids),
        'isRead' => 0,
        'isVisible' => random_int(0,1),
    ];
});

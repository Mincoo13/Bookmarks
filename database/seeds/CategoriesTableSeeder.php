<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'user_id' => 2,
            'name' => 'food',
        ]);
        DB::table('categories')->insert([
            'user_id' => 2,
            'name' => 'animals',
        ]);
        DB::table('categories')->insert([
            'user_id' => 2,
            'name' => 'nature',
        ]);
        DB::table('categories')->insert([
            'user_id' => 1,
            'name' => 'food',
        ]);
        factory(App\Category::class, 100)->create();
    }
}

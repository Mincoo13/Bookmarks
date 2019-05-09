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
            'name' => 'Škola',
        ]);
        DB::table('categories')->insert([
            'user_id' => 2,
            'name' => 'Sociálne siete',
        ]);
        DB::table('categories')->insert([
            'user_id' => 2,
            'name' => 'Vývoj Webu',
        ]);
        DB::table('categories')->insert([
            'user_id' => 1,
            'name' => 'Škola',
        ]);
//        factory(App\Category::class, 100)->create();
    }
}

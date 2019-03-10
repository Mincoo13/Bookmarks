<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comments')->insert([
            'user_id' => 2,
            'bookmark_id' => 2,
            'text' => 'Toto je prvy komentar k bookmarku 2',
        ]);
        DB::table('comments')->insert([
            'user_id' => 2,
            'bookmark_id' => 1,
            'text' => 'Toto je prvy komentar k bookmarku 1',
        ]);
        DB::table('comments')->insert([
            'user_id' => 1,
            'bookmark_id' => 3,
            'text' => 'Toto je prvy komentar k bookmarku 3',
        ]);
        factory(App\Comment::class, 50)->create();

    }
}

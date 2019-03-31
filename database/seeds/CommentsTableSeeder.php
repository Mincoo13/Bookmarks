<?php

use Carbon\Carbon;
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
            'created_at' => Carbon::now(),
        ]);
        DB::table('comments')->insert([
            'user_id' => 2,
            'bookmark_id' => 1,
            'text' => 'Toto je prvy komentar k bookmarku 1',
            'created_at' => Carbon::now(),
        ]);
        DB::table('comments')->insert([
            'user_id' => 1,
            'bookmark_id' => 5,
            'text' => 'Toto je prvy komentar k bookmarku 5',
            'created_at' => Carbon::now(),
        ]);
        DB::table('comments')->insert([
            'user_id' => 1,
            'bookmark_id' => 1,
            'text' => 'Cudzi komentar',
            'created_at' => Carbon::now(),
        ]);
        factory(App\Comment::class, 50)->create();

    }
}

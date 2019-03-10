<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookmarksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 1,
            'name' => 'Materna',
            'link' => 'maternauni.slack.com',
            'isRead' => false,
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 2,
            'name' => 'Facebook',
            'link' => 'facebook.com',
            'isRead' => false,
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 1,
            'category_id' => 4,
            'name' => 'Materna',
            'link' => 'maternauni.slack.com',
            'isRead' => false,
        ]);
        factory(App\Bookmark::class, 150)->create();
    }
}

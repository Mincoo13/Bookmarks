<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Bookmarks_BookmarkListsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 1,
            'bookmarklist_id' => 1,
            'order' => 1,
        ]);
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 2,
            'bookmarklist_id' => 1,
            'order' => 2,
        ]);
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 3,
            'bookmarklist_id' => 1,
            'order' => 3,
        ]);
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 3,
            'bookmarklist_id' => 2,
            'order' => 1,
        ]);
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 4,
            'bookmarklist_id' => 2,
            'order' => 2,
        ]);
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 4,
            'bookmarklist_id' => 1,
            'order' => 4,
        ]);
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 6,
            'bookmarklist_id' => 4,
            'order' => 1,
        ]);
        DB::table('bookmarklists_bookmarks')->insert([
            'bookmark_id' => 5,
            'bookmarklist_id' => 4,
            'order' => 2,
        ]);

//        factory(App\BookmarkList_Bookmark::class, 15)->create();
    }
}

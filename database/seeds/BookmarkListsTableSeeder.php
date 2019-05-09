<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookmarkListsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bookmarklists')->insert([
            'user_id' => 2,
            'name' => 'School',
            'isVisible' => 1,
        ]);
        DB::table('bookmarklists')->insert([
            'user_id' => 2,
            'name' => 'Work',
            'isVisible' => 1,
        ]);
        DB::table('bookmarklists')->insert([
            'user_id' => 2,
            'name' => 'Bachelors',
            'isVisible' => 0,
        ]);
        DB::table('bookmarklists')->insert([
            'user_id' => 1,
            'name' => 'How to admin',
            'isVisible' => 1,
        ]);
        DB::table('bookmarklists')->insert([
            'user_id' => 1,
            'name' => 'Botanics',
            'isVisible' => 0,

        ]);
//        factory(App\BookmarkList::class, 15)->create();
    }
}

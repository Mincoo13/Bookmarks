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
        ]);
        DB::table('bookmarklists')->insert([
            'user_id' => 2,
            'name' => 'Work',
        ]);
        DB::table('bookmarklists')->insert([
            'user_id' => 2,
            'name' => 'Bachelors',
        ]);
        DB::table('bookmarklists')->insert([
            'user_id' => 1,
            'name' => 'How to admin',
        ]);
        factory(App\BookmarkList::class, 15)->create();
    }
}

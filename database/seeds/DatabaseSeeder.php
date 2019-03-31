<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(BookmarksTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        $this->call(BookmarkListsTableSeeder::class);
        $this->call(Bookmarks_BookmarkListsTableSeeder::class);
    }
}

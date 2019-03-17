<?php

use Carbon\Carbon;
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
            'url' => 'maternauni.slack.com',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam tellus dolor, tempor sit amet diam eget, aliquet aliquam lectus. Sed congue eros et lorem imperdiet, semper volutpat nulla rutrum.',
            'isRead' => false,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 2,
            'name' => 'Slack',
            'url' => 'slack.com',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam tellus dolor, tempor sit amet diam eget, aliquet aliquam lectus. Sed congue eros et lorem imperdiet, semper volutpat nulla rutrum.',
            'isRead' => false,
            'isVisible' => false,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 1,
            'name' => 'Google',
            'url' => 'Google.com',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam tellus dolor, tempor sit amet diam eget, aliquet aliquam lectus. Sed congue eros et lorem imperdiet, semper volutpat nulla rutrum.',
            'isRead' => false,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 2,
            'name' => 'Facebook',
            'url' => 'facebook.com',
            'description' => 'Curabitur fringilla ipsum at condimentum bibendum. Aliquam massa leo, dapibus sit amet cursus id, cursus ac arcu. In hac habitasse platea dictumst.',
            'isRead' => false,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 1,
            'category_id' => 4,
            'name' => 'Materna',
            'url' => 'maternauni.slack.com',
            'description' => 'Nullam vel mattis dolor.',
            'isRead' => false,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 1,
            'category_id' => 4,
            'name' => 'Google',
            'url' => 'google.com',
            'description' => 'Nullam vel mattis dolor.',
            'isRead' => false,
            'isVisible' => false,
            'created_at' => Carbon::now(),
        ]);
        factory(App\Bookmark::class, 150)->create();
    }
}

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
            'category_name' => 'Škola',
            'name' => 'Slack BP',
            'url' => 'https://maternauni.slack.com/messages/G9R46T7U5/details',
            'description' => 'Chat s konzultantom prostrecnítvom Slacku.',
            'isRead' => true,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 2,
            'category_name' => 'Sociálne siete',
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/',
            'description' => '',
            'isRead' => false,
            'isVisible' => false,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 1,
            'category_name' => 'Škola',
            'name' => 'Denník BP',
            'url' => 'https://still-depths-81421.herokuapp.com/',
            'description' => 'Denník bakalárskej práce s postupom riešenia.',
            'isRead' => false,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 2,
            'category_name' => 'Sociálne siete',
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/',
            'description' => '',
            'isRead' => true,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 1,
            'category_id' => 4,
            'category_name' => 'Škola',
            'name' => 'Slack',
            'url' => 'https://slack.com/intl/en-sk/',
            'description' => '',
            'isRead' => false,
            'isVisible' => true,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 1,
            'category_id' => 4,
            'category_name' => 'Škola',
            'name' => 'Trello',
            'url' => 'https://trello.com/b/8iHXTngp/bc-bookmarks',
            'description' => 'Taskovací systém.',
            'isRead' => false,
            'isVisible' => false,
            'created_at' => Carbon::now(),
        ]);
        DB::table('bookmarks')->insert([
            'user_id' => 2,
            'category_id' => 4,
            'category_name' => 'Škola',
            'name' => 'Informácie o štúdiu na UIM',
            'url' => 'http://www.uim.elf.stuba.sk/kaivt/',
            'description' => 'UIM zabezpečuje jadro študijného programu Aplikovaná informatika v 3 stupňoch štúdia (Bc., Ing. a PhD.). Okrem toho zabezpečuje fakultné odborné predmety z oblasti informatiky.',
            'isRead' => false,
            'isVisible' => false,
            'created_at' => Carbon::now(),
        ]);
//        factory(App\Bookmark::class, 150)->create();
    }
}

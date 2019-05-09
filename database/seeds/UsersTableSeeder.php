<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'surname' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('Ahoj123!'),
            'isAdmin' => 1,
            'isActive' => 1,
            'created_at' => Carbon::now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Sally',
            'surname' => 'Smith',
            'email' => 'sally.smith@example.com',
            'password' => bcrypt('Sally123!'),
            'isAdmin' => 0,
            'isActive' => 1,
            'created_at' => Carbon::now(),
        ]);
//        factory(App\User::class, 5)->create();
    }
}

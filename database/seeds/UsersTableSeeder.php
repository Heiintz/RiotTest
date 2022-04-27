<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'id' => 1,
            'name' => 'Riot',
            'email' => 'trynda@riot.fr',
            'password' => bcrypt('password')
        ]);
    }
}

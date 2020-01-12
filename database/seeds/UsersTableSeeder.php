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
        //
        DB::table('users')->insert([
            'name' => 'abc',
            'email' => 'abc@def.com',
            // 'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('1234567890'),
        ]);
    }
}

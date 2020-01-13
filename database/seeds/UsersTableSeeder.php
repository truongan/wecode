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
            'username' => 'abc',
            'email' => 'abc@def.com',
            'display_name' => 'Tao la admin',
            // 'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('1234567890'),
            'role_id' => '1' //Admin
        ]);
    }
}

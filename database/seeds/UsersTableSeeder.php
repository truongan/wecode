<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        DB::table('users')->insert([
            'username' => 'testing',
            'email' => 'test@def.com',
            'display_name' => 'Tao la student',
            // 'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('1234567890'),
            'role_id' => '4' //student
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;

class dummy_data extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Insert a bunch of dummy user, assignemnts, and classes for testing
        // $this->call(UsersTableSeeder::class);
        // $this->call(installation_seeding::class);

        for ($i=1; $i < 10; $i++) { 
            DB::table('users')->insert([
                'username' => 'test' . $i,
                'email' => 'test@def.com' . $i,
                // 'email' => Str::random(10).'@gmail.com',
                'password' => Hash::make('1234567890'),
                'role_id' => $i % 4 + 1 //student
            ]);
        }
        for ($i=1; $i < 10; $i++) { 
            DB::table('problems')->insert([
                'name' => 'problem' . $i,
                'is_upload_only' => true,
                'diff_cmd' => $i,
                'diff_arg' => $i,
                'admin_note' => 'fake_data'
            ]);
        }
    }
}

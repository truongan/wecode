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
        // Insert a bunch of dummy user, assignemnts, and classes for testing
        $this->call(UsersTableSeeder::class);
        $this->call(installation_seeding::class);

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
        for ($i=1; $i < 10; $i++) { 
            DB::table('submissions')->insert([
                'username' => 'username' . $i,
                'assignment_id' => $i,
                'problem_id'=>$i,
                'is_final' => 1,
                'time' => new DateTime,
                'status' => $i,
                'pre_score' => '100',
                'coefficient'=>$i,
                'file_name'=>$i,
                'language_id'=>1
            ]);
        }
        for ($i=1; $i < 10; $i++) { 
            DB::table('assignments')->insert([
                'name' => 'username' . $i,
                'total_submits' => $i,
                'open'=>$i,
                'score_board' => $i,
                'javaexceptions' => $i,
                'start_time' => new DateTime,
                'finish_time' => new DateTime,
                'extra_time'=> $i,
                'late_rule'=>$i,
                'participants'=>$i,
                'moss_update'=>$i,
                'created_at'=>new DateTime,
                'updated_at'=>new DateTime
            ]);
        }
    }
}

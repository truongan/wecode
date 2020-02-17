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
        // $this->call(UsersTableSeeder::class);
        // $this->call(installation_seeding::class);

        // for ($i=3; $i < 10; $i++) { 
        //     DB::table('users')->insert([
        //         'username' => 'test' . $i,
        //         'email' => 'test@def.com' . $i,
        //         // 'email' => Str::random(10).'@gmail.com',
        //         'password' => Hash::make('1234567890'),
        //         'role_id' => $i % 4 + 1 //student
        //     ]);
        // }
        // for ($i=1; $i < 10; $i++) { 
        //     DB::table('problems')->insert([
        //         'name' => 'problem' . $i,
        //         'is_upload_only' => true,
        //         'diff_cmd' => $i,
        //         'diff_arg' => $i,
        //         'admin_note' => 'fake_data'
        //     ]);
        // }
        // for ($i=1; $i < 10; $i++) { 
        //     DB::table('submissions')->insert([
        //         'username' => 'username' . $i,
        //         'assignment_id' => $i,
        //         'problem_id'=>$i,
        //         'is_final' => 1,
        //         'time' => new DateTime,
        //         'status' => $i,
        //         'pre_score' => '100',
        //         'coefficient'=>$i,
        //         'file_name'=>$i,
        //         'language_id'=>1
        //     ]);
        // }
        // for ($i=1; $i < 10; $i++) { 
        //     DB::table('assignments')->insert([
        //         'name' => 'username' . $i,
        //         'total_submits' => $i,
        //         'open'=>$i,
        //         'score_board' => $i,
        //         'javaexceptions' => $i,
        //         'start_time' => new DateTime,
        //         'finish_time' => new DateTime,
        //         'extra_time'=> $i,
        //         'late_rule'=>$i,
        //         'participants'=>$i,
        //         'moss_update'=>$i,
        //         'description'=>"....",
        //         'created_at'=>new DateTime,
        //         'updated_at'=>new DateTime
        //     ]);
        // }
        // for ($i=1; $i < 8; $i++) { 
        //     DB::table('language_problem')->insert([
        //        'language_id' => $i,
        //        'problem_id' =>$i,
        //        'time_limit' =>100,
        //        'memory_limit'=>100,
        //        'created_at'=>new DateTime,
        //        'updated_at'=>new DateTime
        //     ]);
        // }
        for ($i=1; $i < 8; $i++) { 
            DB::table('submissions')->insert([
                "username"=>"test".$i,
                "assignment_id"=>$i,
                "problem_id"=>$i,
                "is_final"=>True,
                "time"=>new DateTime,
                "status"=>"1",
                "pre_score"=>"100",
                "coefficient"=>"1",
                "file_name"=>"1",
                "language_id"=>"1",
                "created_at"=>new DateTime,
                "updated_at"=>new DateTime,
            ]);
        }
        // for ($i=1; $i < 8; $i++) { 
        //         DB::table('assignment_problem')->insert([
        //             "assignment_id"=>$i,
        //             "problem_id"=>$i,
        //             "score"=>100,
        //             "ordering"=>$i,
        //             "problem_name"=>$i,
        //             "created_at"=>new DateTime,
        //             "updated_at"=>new DateTime, 
        //         ]);        
        // }
        
    }
}

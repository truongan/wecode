<?php

use Illuminate\Database\Seeder;
use App\Tag;
use App\Problem;
use App\Lop;
use App\Assignment;
use App\User;
use App\Queue_item;
use App\Submission;
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

        for ($i=1; $i < 10; $i++) { 
            DB::table('users')->insert([
                'username' => 'test' . $i,
                'email' => 'test@def.com' . $i,
                'selected_assignment_id' => rand(1,5),
                // 'email' => Str::random(10).'@gmail.com',
                'password' => Hash::make('1234567890'),
                'role_id' => $i % 4 + 1 //student
            ]);
        }
        for ($i=1; $i < 10; $i++) { 
            DB::table('tags')->insert([
                'text' => 'tag' . $i
            ]);
        }
        for ($i=1; $i < 10; $i++) { 
            Problem::create([
                'name' => 'problem' . $i,
                'allow_practice' => (rand(1,10) > 3),
                'diff_cmd' => $i,
                'diff_arg' => $i,
                'admin_note' => 'fake_data'
            ]);
        }

        $a = [1,2,3,4,5,6,7,8];
        for($i = 1; $i < 5; $i++){
            shuffle($a);
            Tag::find($i)->problems()->attach(array_slice($a,0,rand()%5));
        }

        for ($i=1; $i < 10; $i++) { 
            Submission::create([
                'user_id' => rand(1,6),
                'assignment_id' => rand(1,2),
                'problem_id'=>$i,
                'is_final' => rand(0,1),
                'time' => new DateTime,
                'status' => rand(0,100),
                'pre_score' => rand(50,100)/10,
                'coefficient'=>$i,
                'file_name'=>$i,
                'language_id'=>1
            ]);
        }
        for ($i=1; $i < 10; $i++) { 
            Queue_item::create([
                'submission_id' => rand(1,6),
                'type' => array('judge', 'rejudge')[rand(0,1)],
            ]);
        }
        for ($i=1; $i < 10; $i++) { 
            Assignment::create([
                'name' => 'assignment' . $i,
                'total_submits' => $i,
                'open'=>rand(0,1),
                'description' => "",
                'score_board' => rand(0,1),
                'start_time' => new DateTime,
                'finish_time' => new DateTime,
                'extra_time'=> $i,
                'late_rule'=>$i,
                'moss_update'=>$i,
            ]);
        }
        for($i = 1; $i < 5; $i++){
            shuffle($a);
            Assignment::find($i)->problems()->attach([
                rand(1,5) => ['score' => rand(100,200), 'ordering'=>rand(1,10), 'problem_name' => 'dummy 1 for ass ' . $i],
                rand(1,5) => ['score' => rand(100,200), 'ordering'=>rand(1,10), 'problem_name' => 'dummy 2 for ass ' . $i],
                rand(1,5) => ['score' => rand(100,200), 'ordering'=>rand(1,10), 'problem_name' => 'dummy 3 for ass ' . $i],

            ]);
        }
        for ($i=1; $i < 8; $i++) { 
            DB::table('language_problem')->insert([
               'language_id' => $i,
               'problem_id' =>$i,
               'time_limit' =>100,
               'memory_limit'=>100,
               'created_at'=>new DateTime,
               'updated_at'=>new DateTime
            ]);
        }
        for ($i=1; $i < 7; $i++) { 
            DB::table('language_problem')->insert([
               'language_id' => $i+1,
               'problem_id' =>$i,
               'time_limit' =>100,
               'memory_limit'=>100,
               'created_at'=>new DateTime,
               'updated_at'=>new DateTime
            ]);
        }
        for ($i=1; $i < 8; $i++) { 
            Lop::create([
                'name' => 'lop ' . $i,
                'open' => rand()%2,
            ]);
        }

        for($i = 1; $i < 5; $i++){
            shuffle($a);
            Lop::find($i)->assignments()->attach(array_slice($a,0,rand()%5));
        }
        for($i = 1; $i < 5; $i++){
            shuffle($a);
            Lop::find($i)->users()->attach(array_slice($a,0,rand()%7));
        }
    }
}

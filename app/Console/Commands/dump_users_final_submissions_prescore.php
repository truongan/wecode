<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Problem;

class dump_users_final_submissions_prescore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump_users_final_submissions_prescore {idstart} {idend}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::whereBetween('id', [$this->argument('idstart'), $this->argument('idend')])->with('submissions')->get();

        // set_of_problems[$problem_id] = $total_users
        $set_of_problems = [];
        $columns = ['username', 'lop'];
        $output_users = [];
        foreach ($users as $u){
            $output_users[$u->id]['username'] = $u->username;
            $output_users[$u->id]['lop'] = $u->lops()->first()->name ?? "--Leave lop sau khi thi??--";

            foreach ($u->submissions as $sub){
                if ($sub->is_final == 0) continue;
                
                $output_users[$u->id][$sub->problem_id] = $sub->pre_score;
                $set_of_problems[$sub->problem_id] = 1 + ($set_of_problems[$sub->id] ?? 0);
            }
        }
        
        // dd($output_users);
        // array_push($columns, array_keys($set_of_problems));
        $problems = Problem::whereIn('id', array_keys($set_of_problems))->pluck('name', 'id');

        echo ("username,lop");
        foreach ($set_of_problems as $k => $v){
            echo ",{$problems[$k]}";
        }
        echo "\n";
        foreach ($output_users as $out){
            $line = [];
            array_push($line, $out['username']);
            array_push($line, $out['lop']);
            
            foreach ($set_of_problems as $prob_id => $total){
                array_push($line, $out[$prob_id] ?? "" );
            }
            echo (implode(',', $line)) . "\n";
        }
        
        return Command::SUCCESS;
    }
}

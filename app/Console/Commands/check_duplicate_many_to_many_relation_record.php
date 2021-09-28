<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class check_duplicate_many_to_many_relation_record extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_duplicate_many_to_many_relation_record {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplication of many-to-many relation records in database and prune them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        $list_table = [
            'assignment_lop' => ['assignment_id', 'lop_id'], 
            'assignment_problem' => ['assignment_id', 'problem_id'], 
            'language_problem' => ['language_id', 'problem_id'], 
            'lop_user' => ['lop_id', 'user_id'], 
            'problem_tag' => ['problem_id', 'tag_id']
        ];
        foreach ($list_table as $table => $columns){
            var_dump(
                DB::table($table)
                    ->selectRaw(sprintf("min(id) as min_id , count(id) as number_of_rows, %s, %s", $columns[0], $columns[1]))
                    ->groupBy($columns)
                    ->having('number_of_rows', '>', 1)
                    ->get()
            );
        }
        
        if ($this->option('force')){
            foreach ($list_table as $table => $columns){
                $keep_ids = DB::table($table)
                    ->selectRaw(sprintf("min(id) as min_id , count(id) as number_of_rows, %s, %s", $columns[0], $columns[1]))
                    ->groupBy($columns)
                    ->having('number_of_rows', '>', 1)
                    ->get()->pluck('min_id');
                var_dump($keep_ids);

                $t = join(', ', $columns);
                $delete_id = DB::table($table)
                    ->whereRaw(sprintf(' (%s) in ( SELECT %s FROM `lop_user` group by %s having count(id) > 1) ', $t, $t, $t))
                    ->whereNotIn('id', $keep_ids)
                    ->get()->pluck('id');
                
                DB::table($table)->whereIn('id', $delete_id)->delete();

            }
            // var_dump(DB::table('lop_user')->whereRaw(' (lop_id, user_id) in ( SELECT lop_id, user_id FROM `lop_user` group by lop_id, user_id having count(id) > 1) ')->get());
        }
    }
}

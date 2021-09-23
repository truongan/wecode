<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        

        var_dump(DB::table('assignment_lop')->selectRaw('count(id) as number_of_rows, assignment_id, lop_id')->groupBy(['assignment_id', 'lop_id'])->having('number_of_rows', '>' ,1)->get());
        // var_dump(DB::table('assignment_problem')->selectRaw('count(id) as number_of_rows, assignment_id, problem_id')->groupBy(['assignment_id', 'problem_id'])->having('number_of_rows', '>' ,1)->get());
        var_dump(DB::table('language_problem')->selectRaw('count(id) as number_of_rows, language_id, problem_id')->groupBy(['language_id', 'problem_id'])->having('number_of_rows', '>' ,1)->get());
        var_dump(DB::table('lop_user')->selectRaw('count(id) as number_of_rows, lop_id, user_id')->groupBy(['lop_id', 'user_id'])->having('number_of_rows', '>' ,1)->get());
        var_dump(DB::table('problem_tag')->selectRaw('count(id) as number_of_rows, problem_id, tag_id')->groupBy(['problem_id', 'tag_id'])->having('number_of_rows', '>' ,1)->get());
        if ($this->option('force')){

        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lop;
use App\User;
class create_classes_for_user_list extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create_classes_for_user_list {list_file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $list = file_get_contents($this->argument('list_file'));

        $lops_array = [];

        // var_dump(explode(PHP_EOL, $list));
        foreach (explode(PHP_EOL, $list) as $line){
            $line = explode(', ', trim($line));

            if (count($line) != 2) {
                var_dump($line);
                continue;
            } 
            // die();
            $lop = trim( $line[0] );
            $user = trim( $line[1] );

            $lops_array[$lop] ??= [];

            array_push($lops_array[$lop], $user);

        }

        foreach( $lops_array as $lop_name => $stu_list){
            $lop = Lop::firstOrCreate(['name' => $lop_name, 'open' => false]);

            $lop->users()->attach(User::whereIn('username', $stu_list)->pluck('id'));

        }   

        dd($lops_array);
    }
}

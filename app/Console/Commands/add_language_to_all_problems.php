<?php

namespace App\Console\Commands;

use App\Problem;
use App\Language;
use Illuminate\Console\Command;

class add_language_to_all_problems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add_language_to_all_problems';

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
        $a = [];
        foreach (Language::all() as $lang){
            $a[$lang->id] = [
                'time_limit' => $lang->default_time_limit,
                'memory_limit' => $lang->default_memory_limit
            ];
        }
        foreach( Problem::all() as $prob){
            $prob->languages()->attach($a);
            $prob->save();
        }
        return Command::SUCCESS;
    }
}

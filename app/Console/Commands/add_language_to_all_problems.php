<?php

namespace App\Console\Commands;

use App\Models\Problem;
use App\Models\Language;
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
            $b = [];
            $ids = $prob->languages->pluck('id');
            foreach ($a as $lang_id =>  $new_lang){
                if ($ids->search($lang_id) === false) $b[$lang_id] = $new_lang;
            }
            // $prob->languages = [];
            $prob->languages()->syncWithoutDetaching($b);
            $prob->save();
        }
        return Command::SUCCESS;
    }
}

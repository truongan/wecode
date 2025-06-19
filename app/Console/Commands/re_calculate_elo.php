<?php

namespace App\Console\Commands;

use App\Models\Submission;
use Illuminate\Support\Facades\DB;

use Illuminate\Console\Command;

class re_calculate_elo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 're_calculate_elo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */

    private static function prob( $r1,  $r2){
        return 1.0 * 1.0 / (1 + 1.0 * 
        pow(10, 1.0 * ($r1 - $r2) / 400));
    }
    public function handle()
    {
        Submission::with('user', 'problem')->chunk(1000, function ($subs){
            DB::beginTransaction();
            foreach($subs as $sub){
                if ($sub->id %10000 == 0) echo ($sub->id) . "\n";

                $Pb = $this->prob($sub->user->elo, $sub->problem->elo);
  
                // To calculate the Winning
                // Probability of Player A
                $Pa = $this->prob($sub->problem->elo, $sub->user->elo);
              
                $K = 10;
                // Case -1 When Player A wins
                // Updating the Elo $sub->user->elotings
                if ($sub->pre_score == 10000) {
                    $sub->user->elo = $sub->user->elo + $K * (1 - $Pa);
                    $sub->problem->elo = $sub->problem->elo + $K * (0 - $Pb);
                }
              
                // Case -2 When Player B wins
                // Updating the Elo $sub->user->elotings
                else {
                    $sub->user->elo = $sub->user->elo + $K * (0 - $Pa);
                    $sub->problem->elo = $sub->problem->elo + $K * (1 - $Pb);
                }
                $sub->user->save();
                $sub->problem->save();
            }
            DB::commit();
        });
    }
}

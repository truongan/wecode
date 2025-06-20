<?php

namespace App\Console\Commands;

use App\Submission;
use App\Assignment;
use \Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class list_submission_pending_time extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list_submission_pending_time {assignment_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List submissions of an assignment and theirs pending time (based on log file)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ass_id = $this->argument('assignment_id');
        $assignment = Assignment::findOrFail($ass_id);
        $subs  = $assignment->submissions()->with('user')->get();

        $list = array();
        echo '$submission->id, $submission->user->username, $submission->created_at, $start_judging_time,  $judge_run_time, $seconds_to_submit, $total_pending_time', "\n";
        foreach($subs as $submission){
            $submit_path = Submission::get_path($submission->user->username, $ass_id, $submission->problem_id);
            $file_extension = $submission->language->extension;

            $file_path = $submit_path . "/log-{$submission->id}";

            // print($file_path);

            $file_content = file_exists($file_path) ? file_get_contents($file_path) : "File not found";
		    $file_content = trim((mb_convert_encoding($file_content,'UTF-8', 'ISO-8859-1')));
            $file_contents = explode("\n", $file_content);

            // dd($file_contents);
            $start_judging_time = new \Illuminate\Support\Carbon($file_contents[4]);
            $judge_run_time = trim(explode(':', $file_contents[count($file_contents) - 1])[1]);
            // dd($judge_run_time);
            $judge_run_time = intval( explode(' ', $judge_run_time)[0] );

            
            $seconds_to_submit = $submission->created_at->diffInSeconds($assignment->start_time);
            $total_pending_time = $start_judging_time->addMicrosecond($judge_run_time)->diffInSeconds($submission->created_at);

            echo implode(', ', [ 
                $submission->id, $submission->user->username, $submission->created_at, $start_judging_time,  $judge_run_time, $seconds_to_submit, $total_pending_time]), "\n";
            // dd($list);
        }
        

        return Command::SUCCESS;
    }
}

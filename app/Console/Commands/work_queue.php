<?php

namespace App\Console\Commands;

use App\Submission;
use App\Assignment;
use App\Problem;
use App\Queue_item;
use App\Setting;

use Illuminate\Console\Command;

class work_queue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work_queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Working the submissions in the queue';

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
        echo "SHIT";

        // Set correct base_url
		// Because we are in cli mode, base_url is not available, and we get
		// it from an environment variable that we have set in shj_helper.php
	
        $limit = Setting::get('concurent_queue_process', 2);
        

		$item = Queue_item::acquire($limit);
		if ($item === NULL) {
			// Queue is full, exit this process
			var_dump("Exit casue no item");
			exit;
		}

		//To pause the queue when debugging, just exit here
		// exit;

		do { // loop over queue items

			$submit_id = $item->submission->id;
			$username = $item->submission->user->username;
			// $assignment = $item['assignment'];
			// $problem = $this->problem_model->problem_info($item['problem']);
			
			// $type = $item['type'];  // $type can be 'judge' or 'rejudge'
			// $submission = $this->submit_model->get_submission( $assignment, $submit_id);
			// $language = $this->problem_model->all_languages($problem['id'])[$submission['language_id']];

			$file_extension = $language->extension;
			$raw_filename = $item->submission->file_name;

			$tester_path = Setting::get_setting('tester_path', '/');
			
			$problemdir = $submission->problem->get_directory_path();
			
			$userdir = $this->submit_model->get_path($username, $assignment, $problem['id']);
			
			$op1 = $this->settings_model->get_setting('enable_log');

			$time_limit = $language->time_limit/1000;
			$time_limit = round($time_limit, 3);
			$time_limit_int = floor($time_limit) + 1;
			$memory_limit = $language->memory_limit;
			$diff_cmd = $problem['diff_cmd'];
			$diff_arg = $problem['diff_arg'];
			$output_size_limit = $this->settings_model->get_setting('output_size_limit') * 1024;

			$result_file = "$userdir/result-{$submit_id}.html";
			$log_file = "$userdir/log-{$submit_id}";

			// AN - note: Since cmd start bash, this process have to be exit when run from cli to debugg
			$cmd = "cd $tester_path;\n./tester.sh $problemdir $userdir $result_file $log_file ".escapeshellarg($raw_filename)." $file_extension $time_limit $time_limit_int $memory_limit $output_size_limit $diff_cmd '$diff_arg' $op1 ";

			file_put_contents($userdir.'/log', $cmd);

			///////////////////////////////////////
			// Running tester (judging the code) //
			///////////////////////////////////////
			putenv('LANG=en_US.UTF-8');
			putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games');
			putenv('APP_ENV=local');
			$output = trim(shell_exec($cmd));


			// Deleting the jail folder, if still exists. Cannot run along wtih multiple concurent queueprocess
			// shell_exec("cd $tester_path; rm -rf jail*");

			
			// Saving judge result
			if (is_numeric($output)) {
				$submission['pre_score'] = $output;
				$submission['status'] = 'SCORE';
			}
			else {
				$submission['pre_score'] = 0;
				$submission['status'] = $output;
			}
			var_dump($output);

			//reconnect to database incase we have run test for a long time.
			$this->db->reconnect();

			// Save the result
			$this->queue_model->save_judge_result_in_db($submission, $type);

			// Remove the judged item from queue
			$this->queue_model->remove_item($item['id']);

			// Get next item from queue
			$item = $this->queue_model->acquire($limit);

		}while($item !== NULL);

		var_dump("Exit, no more item");
    }
}

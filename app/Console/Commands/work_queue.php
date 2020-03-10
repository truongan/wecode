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
        // echo "SHIT";

        // Set correct base_url
		// Because we are in cli mode, base_url is not available, and we get
		// it from an environment variable that we have set in shj_helper.php
	
        $limit = Setting::get('concurent_queue_process', 2);
        // dd($limit);

		$item = Queue_item::acquire($limit);
		if ($item === NULL) {
			// Queue is full, exit this process
			var_dump("Exit casue no item");
			exit;
		}

		//To pause the queue when debugging, just exit here
		// exit;

		do { // loop over queue items

			// var_dump($item);

			$submit_id = $item->submission->id;
			$username = $item->submission->user->username;

			$language = $item->submission->problem->languages->find($item->language_id);
			
			$userdir = $item->submission->directory();
			
			$result_file = "$userdir/result-{$submit_id}.html";
			$log_file = "$userdir/log-{$submit_id}";
			
			$tester_path = Setting::get('tester_path', '/');
			$problemdir = $item->submission->problem->get_directory_path(); 
			
			
			if ($language == NULL){
				$item->submission->pre_score = 0;
				$item->submission->status = 'SCORE';

				file_put_contents($log_file, "INVALID LANGUAGE");
				file_put_contents($result_file, "INVALID LANGUAGE");

			}
			else {

				$file_extension = $language->extension;
				$raw_filename = $item->submission->file_name;
				
				$time_limit = $language->time_limit/1000;
				$time_limit = round($time_limit, 3);
				$time_limit_int = floor($time_limit) + 1;
				
				$memory_limit = $language->memory_limit;
				$diff_cmd = $item->problem->diff_cmd;
				$diff_arg = $item->problem->diff_arg;
				
				$output_size_limit = Setting::get('output_size_limit') * 1024;
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
				
				if (is_numeric($output)) {
					$item->submission->pre_score = $output;
					$item->submission->status = 'SCORE';
				}
				else {
					$item->submission->pre_score = 0;
					$item->submission->status = $output;
				}
				
				var_dump($output);
			}

			$item->save_and_remove();

			// Get next item from queue
			$item = Queue_item::acquire($limit);

		}while($item !== NULL);

		var_dump("Exit, no more item");
    }
}

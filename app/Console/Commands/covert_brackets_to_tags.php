<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Problem;
use App\Models\Tag;

class covert_brackets_to_tags extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:covert_brackets_to_tags {--d|dry_run}';

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
		$all_prob = Problem::pluck('name', 'id');
        echo "SHIT";
		foreach ($all_prob as $id => $name){
			$count = preg_match_all('/\[(.*?)\]/', $name, $matches);
			// if( $count == 0 ) continue;
			$prob = Problem::find($id);


			$new_name = preg_replace('/\[(.*?)\]/', '', $name);
            // $matches[1][] = $prob->user->username; //Add uploader name as a tags
            // Now that we have search be owner name, this is not very useful anymore
			echo ("Problem {$id} got the following brackets: " . print_r($matches[1], true) . "rename to {$new_name}\n\n");

			if ($this->option('dry_run')) continue;

			$tags = [];
			foreach ($matches[1] as $tag_name){
				$tags[] = Tag::firstOrCreate([
					'text' => $tag_name
				]);
			}
			$prob->tags()->syncWithoutDetaching($tags);
			$prob->name = $new_name;
			$prob->save();
		}
		// process squre bracket (the most common one)
	}
}

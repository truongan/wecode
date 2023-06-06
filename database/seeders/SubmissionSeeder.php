<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Submission;
use \App\Scoreboard;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 2 dummy submissions
<<<<<<< Updated upstream
        Submission::factory(100)->create();
=======
        Submission::factory(40)->create();
>>>>>>> Stashed changes
        // Remember to add assignment_id
        Scoreboard::update_scoreboard(5);
    }
}

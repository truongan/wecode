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

        Submission::factory(100)->create();

        // Remember to add assignment_id
        Scoreboard::update_scoreboard(3);
    }
}

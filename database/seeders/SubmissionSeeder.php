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
        Submission::factory(2)->create();
        echo ('SubmissionSeeder seeded successfully!\n');
        // Remember to add assignment_id
        Scoreboard::update_scoreboard(2);
        echo ('Updated scoreboard successfully!');

    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Submission;
use \App\Problem;
use \App\Assignment;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $last_assignment_id = Assignment::latest('id')->first()->id;
        $last_problem_id = Problem::latest('id')->first()->id;
        // Random user_id in range
        $rand_user_id = rand(3,13);

        // Random assignment_id in range
        $assignment_id = rand(2,$last_assignment_id);
        // Random problem_id in range
        $problem_id = rand(1, $last_problem_id);

        return [
            'assignment_id' => $assignment_id,
            'problem_id' => $problem_id,
            'user_id' => $rand_user_id,
            // Make sure only one is_final = 1 per user's list of submissions
            'is_final' => (Submission::where([
                ['user_id',$rand_user_id],
                ['problem_id', $problem_id],
                ['assignment_id',$assignment_id],
                ['is_final',1]
            ])->count()) ? 0 : 1,
            'status' => 'SCORE',
            'pre_score' => rand(0,10)*1000,
            'coefficient' => 100,
            'file_name' => '',
            'language_id' => 2,
            'judgement' => json_decode("{}"),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Submission;
use \App\Problem;
use \App\Assignment;
use \App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;

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
        $rand_assignment_id = Assignment::all()->random()->id;
        $rand_problem_id = Problem::all()->random()->id;
        $rand_user_id = User::all()->random()->id;

        return [
            'assignment_id' => 2,
            'problem_id' => $rand_problem_id,
            'user_id' => $rand_user_id,
            'is_final' => 0,
            'status' => 'SCORE',
            'pre_score' => rand(1,10)*1000,
            'coefficient' => 100,
            'file_name' => '',
            'language_id' => 2,
            'judgement' => json_decode("{}"),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Submission $submission) {
            $freeze_time = Assignment::where('id', $submission->assignment_id)->value('freeze_time');
            $fullmark_sub = Submission::where([
                ['user_id',$submission->user_id],
                ['problem_id', $submission->problem_id],
                ['assignment_id',$submission->assignment_id],
                ['is_final',1],
                ['pre_score', 10000],
                ['created_at', '<', $freeze_time]
            ])->first();
            if (!$fullmark_sub) {
                Submission::where([
                    ['user_id',$submission->user_id],
                    ['problem_id', $submission->problem_id],
                    ['assignment_id',$submission->assignment_id],
                    ['is_final',1],
                ])->update(['is_final' => 0]);
                $submission->update(['is_final' => 1]);
            }
        });
    }
}

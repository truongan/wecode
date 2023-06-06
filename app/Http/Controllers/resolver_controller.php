<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Scoreboard;
use App\Assignment;
use App\Submission;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class resolver_controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($assignment_id)
    {
        $assignment = Assignment::find($assignment_id);

        if ($assignment) {

            // All final submissions of all users
            // $collection_of_submissions = Submission::where([['assignment_id', $assignment_id], ['is_final', 1]])->get();
            // Log::info($collection_of_submissions);

            //===========================================================

            // Key: ordering, value: id
            $array_of_problems_id = array();
            $collection_of_problems = $assignment->problems;
            foreach ($collection_of_problems as $index => $item) {
                $array_of_problems_id[$item->pivot->ordering] = $item->id;
            }
            // Log::info($array_of_problems_id);

            //===========================================================


            // Key: id, value: username
            $array_of_usernames = array();
            $collection_of_users_id = Submission::where([
                ['assignment_id', $assignment_id], 
                ['is_final', 1]])->get();
            foreach ($collection_of_users_id as $index => $item) {
                $query = User::where('id', $item->user_id)->first('username');
                // Log::info($query->username);
                $array_of_usernames[$item->user_id] = $query->username;
            }
            // Log::info($array_of_usernames);

            //===========================================================


            // Key: "username", value: ["tries_before" => ["key" => tries],
            //                          "tries_during" => ["key" => tries]]
            $array_of_tries = array();
            foreach ($array_of_usernames as $id => $username) {
                $array_of_tries_before = array();
                $array_of_tries_during = array();
            
                foreach ($array_of_problems_id as $problem_id) {
                    $num_of_sub_before = Submission::where([
                        ['assignment_id', $assignment_id], 
                        ['user_id', $id], 
                        ['problem_id', $problem_id],
                        ['created_at', '<', $assignment->freeze_time]])->get()->count();
                    $num_of_sub_during = Submission::where([
                        ['assignment_id', $assignment_id], 
                        ['user_id', $id], 
                        ['problem_id', $problem_id],
                        ['created_at', '>=', $assignment->freeze_time]])->get()->count();

                    // Log::info($num_of_sub_before);
                    // Log::info($num_of_sub_during);

                    $array_of_tries_before[$problem_id] = $num_of_sub_before;
                    $array_of_tries_during[$problem_id] = $num_of_sub_during;
                }

                $array_of_tries[$username] = array(
                    'tries_before' => $array_of_tries_before,
                    'tries_during' => $array_of_tries_during,
                );
            }
            // Log::info($array_of_tries);


            //===========================================================

            // Key: "username", value: ["key" => time]
            // Key: "username", value: ["key" => accepted]
            $array_of_accepted_time = array();
            $array_of_accepted = array();
            foreach ($array_of_usernames as $id => $username) {
                $array_of_accepted_time_per_prob = array();
                $array_of_accepted_per_prob = array();

                foreach ($array_of_problems_id as $problem_id) {
                    $submission = Submission::where([
                        ['assignment_id', $assignment_id], 
                        ['is_final', 1], 
                        ['user_id', $id], 
                        ['problem_id', $problem_id]])->first();
                    if ($submission && $submission->pre_score == 10000) {
                        // $score = ceil($submission->pre_score/$submission->coefficient);
                        $time = $assignment->start_time->diffInMinutes($submission->created_at, true);

                        // Penalty
                        $penalty = ($array_of_tries[$username]['tries_before'][$problem_id] + $array_of_tries[$username]['tries_during'][$problem_id]-1) * \App\Setting::get('submit_penalty');
                        // Log::info($penalty);
                        $time += $penalty;

                        $accepted = true;
                    } else {
                        $time = 0;
                        $accepted = false;
                    }
                    $array_of_accepted_time_per_prob[$problem_id] = $time;
                    $array_of_accepted_per_prob[$problem_id] = $accepted;
                }

                $array_of_accepted_time[$username] = $array_of_accepted_time_per_prob;
                $array_of_accepted[$username] = $array_of_accepted_per_prob;
            }
            // Log::info($array_of_accepted_time);
            // Log::info($array_of_accepted);

            //====================================================================

            $usernames = array();
            $total_accepted_times = array();
            $total_accepted = array();
            $total_tries = array();
            $name_schools = array();
            $images = array();

            foreach($array_of_usernames as $id => $username) {
                array_push($usernames, $username);

                $user = User::where('id', $id)->first(['Name_school']);
                if ($user) {
                    $name_school = $user->Name_school;
                    $name_schools[] = $name_school;
                }

                $user = User::where('id', $id)->first(['image']);
                if ($user) {
                    $image = $user->image;
                    $images[] = $image;
                }

                $submission_before = Submission::where([
                    ['assignment_id', $assignment_id], 
                    ['is_final', 1], 
                    ['user_id', $id], 
                    ['created_at', '<', $assignment->freeze_time]])->get();
                
                // Log::info($submission_before);

                $total_accepted_time = 0;
                $total_ac = 0;
                $total_try = 0;
                if ($submission_before) {
                    foreach ($submission_before as $item) {
                        $total_try += 1;
                        if ($item->pre_score != 10000) {
                            continue;
                        }
                        $time = $assignment->start_time->diffInMinutes($item['created_at'], true);
                        
                        // Log::info($username);
                        // Log::info($item->problem_id);
                        // Log::info(($item->pre_score == 10000));
                        // Log::info($array_of_tries[$username]['tries_before'][$item->problem_id]);

                        $penalty = ($array_of_tries[$username]['tries_before'][$item->problem_id]-1) * \App\Setting::get('submit_penalty');
                        $time += $penalty;
                        // Log::info($penalty);
                        // Log::info($time);

                        $total_ac += 1;
                        $total_accepted_time += $time;
                        // Log::info($total_ac);
                        // Log::info($total_ac);
                    }
                }

                // Log::info($total_score_before);
                array_push($total_accepted_times, $total_accepted_time);
                array_push($total_accepted, $total_ac);
                array_push($total_tries, $total_try);

            }
            
            // Log::info($usernames);
            // Log::info($total_scores);




            // $array_of_accepted_scores = array();
            // $array_of_solved = array();
            // $array_of_penalty = array();    
            // foreach ($array_of_usernames as $id => $username) {
            //         $total_prescore = Submission::where([
            //             ['assignment_id', $assignment_id], 
            //             ['is_final', 1], 
            //             ['user_id', $id], 
            //             ['created_at', '<', $assignment->freeze_time]])->get()->sum('pre_score');

            //         $total_score = $to

            //         $accepted_score = Submission::where([
            //             ['assignment_id', $assignment_id], 
            //             ['user_id', $id], 
            //             ['problem_id', $problem_id],
            //             ['created_at', '>=', $assignment->freeze_time]])->get()->count();

            //         // Log::info($num_of_sub_before);
            //         // Log::info($num_of_sub_during);

            //         array_push($array_of_tries_before, $num_of_sub_before);
            //         array_push($array_of_tries_during, $num_of_sub_during);
            //     }

            //     $array_of_tries[$username] = array(
            //         'tries_before' => $array_of_tries_before,
            //         'tries_during' => $array_of_tries_during,
            //     );
            // }

            $data = array(
                'username' => $usernames,
                'accepted_time' => $total_accepted_times,
                'accepted' => $total_accepted,
                'tries_to_solve' => $total_tries,
                'school_name' => $name_schools,
                'image' => $images
            );

            array_multisort(
                $data['accepted'], SORT_NATURAL, SORT_DESC,
                $data['accepted_time'], SORT_NATURAL, SORT_ASC,
                $data['username'],
                $data['tries_to_solve'],
                $data['school_name'],
                $data['image']
            );

            // dd($data);

            return view('resolver', [
                'selected' => 'resolver',
                'data' => $data,
                'tries' => $array_of_tries,
                'accepted_time' => $array_of_accepted_time,
                'accepted' => $array_of_accepted,
                'problem_id' => $array_of_problems_id,
                // 'scores' => $array_of_score,
                // 'tries' => $array_of_tries,
                // 'accepted' => $array_of_accepted,
            ]);
        }
    }
}

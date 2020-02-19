<?php

namespace App\Http\Controllers;

use App\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class submission_controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $final = 0)
    {
        if ( in_array( Auth::user()->role->name, ['student']) )
        {
            $submissions = Submission::where('user_id',Auth::user()->id)->get();
            if ($final == 1)
                $submissions = $submissions->where('is_final',1)->get();
            return view('submissions.list',['submissions' => $submissions, 'final' => $final, 'selected' => 'submissions']);
        }
        else 
        {
            if ($final == 1)
                $submissions = Submission::where('is_final',1)->get();
            return view('submissions.list',['submissions' => Submission::all(), 'final' => $final, 'selected' => 'submissions']); 
        }
    }

    public function indexx(int $final = 0)
    {
        echo("Hello");
    }
}

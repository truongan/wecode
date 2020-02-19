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
    public function index()
    {
        if ( in_array( Auth::user()->role->name, ['student']) )
        {
            $submissions = Submission::where($user_id,Submission::user()->user_id)->get();
            return view('submissions.list',['submissions' => $submissions, 'selected' => 'submissions']);
        }
        return view('submissions.list',['submissions' => Submission::all(), 'selected' => 'submissions']); 
    }

}

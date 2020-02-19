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
    public function index($choose = 'all')
    {
        if ( in_array( Auth::user()->role->name, ['student']) )
        {
            if ($choose == 'final')
                $submissions = Submission::where('user_id',Auth::user()->id)->where('is_final',1)->get();
            else
                $submissions = Submission::where('user_id',Auth::user()->id)->get();
            return view('submissions.list',['submissions' => $submissions, 'choose' => $choose, 'selected' => 'submissions']);
        }
        else 
        {
            if ($choose == 'final')
                $submissions = Submission::where('is_final',1)->get();
            else  $submissions = Submission::all();
            return view('submissions.list',['submissions' => $submissions, 'choose' => $choose, 'selected' => 'submissions']); 
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Lop;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class lop_controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth'); // phải login
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
        //     abort(403);
        if (Auth::user()->role->name == 'admin'){

            return view('admin.lops.list', ['lops' => Lop::latest()->get()]);
        } else {
            
            return view('admin.lops.list', ['lops' => Lop::available(Auth::user()->id)->latest()-get()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403);
        return view('admin.lops.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403);
        //
        // var_dump($request->input());die();
        $a = $request->only('name');
        $a['open'] = $request->input('open') == 'on';

        $new = Lop::create($a);
        $usernames = preg_split("/[\s,]+/", $request->input('user_list'));
        if($usernames != []){
            $users = User::WhereIn('username', $usernames)->get();
            $userids = $users->reduce(function($carry, $i){
                array_push($carry, $i->id);
                return $carry;
            }, []);
            
            $new->users()->sync($userids);
        }
        $new->users()->syncWithoutDetaching(Auth::user()->id); //The user creating classes will be auto enrol

        return redirect()->route('lops.show', ['lop' => $new]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lop  $lop
     * @return \Illuminate\Http\Response
     */
    public function show(Lop $lop)
    {
        //
        // if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            // abort(403);
        return view('admin.lops.edit', ['lop' => $lop]);
    }

    public function scoreboard(Lop $lop){
		// DB::enableQueryLog();
        if ( in_array( Auth::user()->role->name, ['student', 'instructor']) )
            abort(403);
        if (!in_array( Auth::user()->role->name, ['admin']) 
            && !Auth::user()->lops->contains($lop)
        ) abort(403, 'You can only view scoreboard the classes you are in');
        
        $user_table = [];
        foreach ($lop->assignments as $assignment) {
            $submissions = $assignment->submissions()->where('is_final',1)->get();

            $problems = $assignment->problems->keyBy('id');
            foreach ($submissions as $key => $submission) {
                $pre_score = ceil(
                    $submission->pre_score*
                    ($problems[$submission->problem_id]->pivot->score ?? 0 )/10000
                );
                if ($submission['coefficient'] === 'error') $final_score = 0;
                else $final_score = ceil($pre_score*$submission['coefficient']/100);

                // dd($submission['created_at']);
                $fullmark = ($submission->pre_score == 10000);

                $user_table[$submission->user_id][$assignment->id]['score']  ??= 0;
                $user_table[$submission->user_id][$assignment->id]['accept_score'] ??= 0;

                $user_table[$submission->user_id][$assignment->id]['score']  += $final_score;
                $user_table[$submission->user_id][$assignment->id]['accept_score'] += $fullmark ? $final_score : 0;
            }
        }
        foreach ($user_table as $uid => $user) {
            foreach ($user as $aid => $ass) {
                $user_table[$uid]['sum_ac'] ??= 0;
                $user_table[$uid]['sum'] ??= 0;

                $user_table[$uid]['sum_ac'] += $ass['accept_score'];
                $user_table[$uid]['sum'] += $ass['score'];
            }
        }

        // dd($user_table);
        // dd(DB::getQueryLog());
        return view('admin.lops.scoreboard',['lop'=>$lop, 'user_table' => $user_table]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lop  $lop
     * @return \Illuminate\Http\Response
     */
    public function edit(Lop $lop)
    {
        if ( in_array( Auth::user()->role->name, ['student', 'instructor']) )
            abort(403);
        if (!in_array( Auth::user()->role->name, ['admin']) 
            && !Auth::user()->lops->contains($lop)
        ) abort(403, 'You can only edit the classes you are in');
        
        return view('admin.lops.edit', ['lop' => $lop]);
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lop  $lop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lop $lop)
    {
        if ( in_array( Auth::user()->role->name, ['student', 'instructor']) )
            abort(403);
        if (!in_array( Auth::user()->role->name, ['admin']) 
            && !Auth::user()->lops->contains($lop)
        ) abort(403, 'You can only edit the classes you are in');
        
        $data = $request->only('name');
        $data['open'] = $request->input('open') == 'on';

        $lop->update($data);

        // var_dump($request->input());die();

        $remove = $request->input('remove');
        // dd(array_search(Auth::user()->id,$remove));
        if($remove != NULL){
            $find = array_keys($remove, Auth::user()->id);
            foreach ($find as $key) {
                unset($remove[$key]);
            }
            $lop->users()->detach($remove);
        }

        $usernames = preg_split("/[\s,]+/", $request->input('user_list'));
        if($usernames != []){
            // $users = User::WhereIn('username', $usernames)->get();
            $userids = User::WhereIn('username', $usernames)->get()->pluck('id');
            // $users->reduce(function($carry, $i){
            //     array_push($carry, $i->id);
            //     return $carry;
            // }, []);
            
            $lop->users()->syncWithoutDetaching($userids);
        }

        return redirect()->route('lops.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lop  $lop
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403);
        else if (!in_array( Auth::user()->role->name, ['admin']) 
                && !Auth::user()->lops->pluck('id')->contains($id)
        ) abort(403, 'You can only delete the classes you are in');
        elseif ($id === NULL)
			$json_result = array('done' => 0, 'message' => 'Input Error');
        else
        {
            Lop::find($id)->assignments()->sync([]);
            Lop::destroy($id);
            $json_result = array('done' => 1);
        }
        header('Content-Type: application/json; charset=utf-8');  
        return ($json_result);
    }

    public function enrol(Request $request, Lop $lop, $in){
        if($in == 1){
            if ($lop->open == 1) $lop->users()->syncWithoutDetaching(Auth::user()->id);
        }
        else {
            $lop->users()->detach(Auth::user()->id);
        }
        return redirect()->back();
    }
}

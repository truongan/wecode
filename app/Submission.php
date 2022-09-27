<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Setting;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Submission extends Model
{
    protected $fillable = ['id', 'user_id','assignment_id','problem_id','is_final','time','status','pre_score'
                            ,'coefficient','file_name','language_id', 'judgement'];
    protected $casts = [
        'judgement' => 'object'
    ];
    public function problem()
    {
        return $this->belongsTo('App\Problem');
    }

    public function language()
    {
        return $this->belongsTo('App\Language');
    }

    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }
    
    public function user()
    {
    	return $this->belongsTo('App\User');
    }
    
	public static function get_path($username, $assignment_id, $problem_id)
    {
        $assignment_root = rtrim(Setting::get("assignments_root"),'/');
        return $assignment_root . "/assignment_{$assignment_id}/problem_{$problem_id}/{$username}";
    }

	public function directory(){
		return Submission::get_path($this->user->username, $this->assignment_id, $this->problem_id);
	}


    public function get_judgement_from_result_html(){
        $result = mb_convert_encoding( file_get_contents( $this->directory() . "/result-" . $this->id . ".html"), 'UTF-8');
        $results = explode("</span>\n", $result);
        
        $times_and_mem = Arr::flatten(array_filter($results, function($i){return str_contains($i, 'text-muted');}));
        $times = array_map(function($i){ return floatval( Str::before(Str::after($i, "<small>"), " s and")  )  ;},  $times_and_mem);
        $mems = array_map(function($i){ return floatval( Str::before(Str::after($i, "s and "), " KiB")  )  ;},  $times_and_mem);
        
        $testcase_verdict = array_filter($results, function($s){
            return $s != '' && !Str::contains($s, ['text-muted', 'text-primary', 'text-success']);
        });

        $testcase_verdict = array_map( function($s) {return Str::before( Str::after($s, '>'), "<" );}, $testcase_verdict );
        $verdicts = [];
        foreach ($testcase_verdict as $key => $value) {
            $verdicts[$value] = ($verdicts[$value] ?? 0) + 1;
        }

        $a = [ "times" => $times, "mems" => $mems, "verdicts" => $verdicts ]  ;
        return $a;
    }

    public static function get_final_submissions($assignment_id)
    {
        if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            return DB::table('users')->join('submissions', 'users.id', '=', 'submissions.user_id')->select('users.username', 'submissions.*')->where(['assignment_id' => $assignment_id, 'is_final' => 1])->orderBy('username','asc')->orderBy('problem_id','asc')->get();
        else
            return DB::table('users')->join('submissions', 'users.id', '=', 'submissions.user_id')->select('users.username', 'submissions.*')->where(['assignment_id' => $assignment_id, 'is_final' => 1, 'username' => Auth::user()->username])->orderBy('username','asc')->orderBy('problem_id','asc')->get();
    }
}

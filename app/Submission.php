<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Setting;
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

    public static function get_final_submissions($assignment_id)
    {
        if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            return DB::table('users')->join('submissions', 'users.id', '=', 'submissions.user_id')->select('users.username', 'submissions.*')->where(['assignment_id' => $assignment_id, 'is_final' => 1])->orderBy('username','asc')->orderBy('problem_id','asc')->get();
        else
            return DB::table('users')->join('submissions', 'users.id', '=', 'submissions.user_id')->select('users.username', 'submissions.*')->where(['assignment_id' => $assignment_id, 'is_final' => 1, 'username' => Auth::user()->username])->orderBy('username','asc')->orderBy('problem_id','asc')->get();
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Setting;
class Submission extends Model
{
    protected $fillable = ['id', 'user_id','assignment_id','problem_id','is_final','time','status','pre_score'
                            ,'coefficient','file_name','language_id'];

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
}

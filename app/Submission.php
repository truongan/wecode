<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Submission extends Model
{
    protected $fillable = ['id', 'username','assignment_id','problem_id','is_final','time','status','pre_score'
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

}

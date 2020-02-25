<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Problem extends Model
{
    protected $fillable = ['id','name','is_upload_only','diff_cmd','diff_arg'];


    public function languages()
    {
        return $this->belongsToMany('App\Language')->withTimestamps()->withPivot('time_limit','memory_limit');
    }

    public  function problem_info_detailed(){
        
        $query = Problem::find($id);
        
        $result['no_of_ass'] = $this->assigments->count();
        $result['no_of_sub'] = $this->submissions->count();
        
        if($result != NULL) $result['languages'] = $this->all_languages;
       
        return $result;
	}

    public function assignments()
    {
        return $this->belongsToMany('App\Assignment');
    }

    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
}

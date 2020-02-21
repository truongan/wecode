<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Problem extends Model
{
    protected $fillable = ['id','name','is_upload_only','diff_cmd','diff_arg'];

    public static function problem_info($id = NULL){
        $query = Problem::where('id', $id)->first();
		if($query != NULL) $query['languages'] = Problem::all_languages($id);
		return $query;
	}

    public static function all_languages($id)
    {
        $result = [];
        $query = Problem::find($id);
        foreach ($query->languages as $language_name)
            array_push($result,$language_name->name);
        return $result;
    }

    public function languages()
    {
        return $this->belongsToMany('App\Language')->withTimestamps();
    }

    public  function problem_info_detailed(){
        
        if ($id === NULL) return NULL;
        $query = Problem::find($id);
        
        $result['no_of_ass'] = $this->assigments->count();
        $result['no_of_sub'] = $this->submissions->count();
        
        if($result != NULL) $result['languages'] = Problem::all_languages($id);
       
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

}

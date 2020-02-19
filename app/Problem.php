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
        foreach ($query->get_id as $language_name)
            array_push($result,$language_name->name);
        return $result;
    }

    public function get_id()
    {
        return $this->belongsToMany('App\Language');
    }

    public static function problem_info_detailed($id = NULL){
        $query = Problem::find($id);
        
        $result['no_of_ass'] = $query->map_with_assigment->count();
        $result['no_of_sub'] = $query->map_with_submission->count();
        if($result != NULL) 
            $result['languages'] = Problem::all_languages($id);
        return $result;
	}

    public function map_with_assigment()
    {
        return $this->belongsToMany('App\Assignment');
    }

    public function map_with_submission()
    {
        return $this->hasMany('App\Submission');
    }

}

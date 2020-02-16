<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    
    
}

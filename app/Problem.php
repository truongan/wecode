<?php

namespace App;

use App\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Problem extends Model
{
    protected $fillable = ['id','name','diff_cmd','diff_arg','allow_practice','admin_note','difficult','user_id', 'sharable'];

    public function get_directory_path(){
		$assignments_root = Setting::get("assignments_root");
        $problem_dir = $assignments_root . "/problems/".$this->id;
        return $problem_dir;
    }


    public function template_path($language_extension = 'cpp'){
        $pattern1 = rtrim($this->get_directory_path()
		."/template.public." . $language_extension);

		$template_file = glob($pattern1);
		if ( ! $template_file ){
			$pattern = rtrim($this->get_directory_path()
						."/template." . $language_extension);

			$template_file = glob($pattern);
		}
		return $template_file;
    }

    public function template_content($language_extension = 'cpp'){
        $file_glob = $this->template_path($language_extension);
        if ($file_glob){
            $template = file_get_contents($file_glob[0]);
            return $template;
        } 
        else return NULL;
    }

    function user(){
        return $this->belongsTo('App\User');
    }
    function owner(){
        return $this->belongsTo('App\User');
    }

    public static function available($user_id){
        return Problem::where(['sharable'=>1])->orWhere('user_id', $user_id);
    }
    
    public function languages()
    {
        return $this->belongsToMany('App\Language')->withTimestamps()->withPivot('time_limit','memory_limit');
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

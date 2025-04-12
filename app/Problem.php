<?php

namespace App;

use App\Setting;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Problem extends Model
{
    protected $fillable = ['id','name','diff_cmd','diff_arg','allow_practice','admin_note','difficult','user_id', 'sharable','author','editorial', 'allow_input_download', 'allow_output_download'];

    protected $casts = [
        'allow_practice' => 'boolean',
        'sharable' => 'boolean',
        'allow_input_download' => 'boolean',
        'allow_output_download' => 'boolean',
    ];
    public function get_directory_path(){
		$assignments_root = Setting::get("assignments_root");
        $problem_dir = $assignments_root . "/problems/".$this->id."/";
        return $problem_dir;
    }


    public function delete(){
        // If you want to set transaction time, you can append the new argument in the transaction function
		DB::beginTransaction();  
        
		Submission::where('problem_id', $this->id)->delete();
        
		
		$this->languages()->detach();
		$this->assignments()->detach();
        
		$this->tags()->detach();
        
		parent::delete();
		DB::commit();
		
		
		// Delete assignment's folder (all test cases and submitted codes)
		$cmd = 'rm -rf '. $this->get_directory_path();

		shell_exec($cmd);

    }


    public function can_practice(User $user){
        if ($user->role->name == 'admin') return true;
        if ($user->id == $this->user->id) return true;
        if ($this->allow_practice) return true;
        if ($this->sharable &&  in_array( $user->role->name, ['head_instructor', 'instructor']) ) return true;
        return false;
    }

    public function can_edit(User $user){
        if ( ! in_array( $user->role->name, ['admin']) )
        {
            //Admin can always edit
            if ($this->user->id != $user->id){
                //Others can only edit problems they own
                return false;
            } 
        }
        return true;
    }

    /** Dowload file pdf  */
    public function pdf()
    {
        // Find pdf file
        $pattern = $this->get_directory_path()."/*.pdf";
            
        $pdf_files = glob($pattern);
        $pdf_files = implode("|",$pdf_files);
        if ( ! $pdf_files )
            abort(404,"File not found");

        // Download the file to browser
        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/pdf',
        ];
        return response()->file($pdf_files, $headers);

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

    public function description(){
       
        $problem_dir = $this->get_directory_path($this->id);
        
		$result =  array(
			'description' => '<p>Description not found</p>',
			'has_pdf' => glob("$problem_dir/*.pdf") != FALSE,
			'has_template' => glob("$problem_dir/template.cpp") != FALSE
        );
		
		$path = "$problem_dir/desc.html";
        
		if (file_exists($path))
            $result['description'] = file_get_contents($path);   
       
		return $result;
    }

    public function template_content($language_id){
        $language_extension = $this->languages()->find($language_id)->extension;
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Setting;

class html_editor_controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
		$this->autosave = Setting::get('assignments_root') . "/htmleditor.auto.save.txt";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);
        $content = '';
        if (file_exists($this->autosave)) {
            $content = file_get_contents($this->autosave);
        }
        if ($content == "")  
        $content = '<p>Just click on the text to start editing. You can insert formula <span class="math-tex">\(x = {-b \pm \sqrt{b^2-4ac} \over 2a}\)</span>or image as well.</p>';        
        file_put_contents($this->autosave, $content);    
        //var_dump($content); die();
        return view('html_edit', ['selected' => 'settings', 'content' => $content]);
    }  

    public function autosave(Request $request){
        // $this->form_validation->set_rules('content', 'content', 'required');    
        // if ($this->form_validation->run())
        // {
            $content = $request["content"];
            $a =file_put_contents($this->autosave, $content); 
            if ($a){                    
                echo "success";
                return;
            }
        // }    
        // echo "shit";
        // show_error("saving fail", 403);
    }
}

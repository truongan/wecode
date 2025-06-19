<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class html_editor_controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $autosave;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // We need this so that the 'auth' middleware will run before the following line of code
            $this->autosave  = sprintf('%s/%s/_htmleditor.auto.save.txt', Setting::get('assignments_root'), Auth::id());
            return $next($request);
        });
        
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
        if (!file_exists(pathinfo($this->autosave, PATHINFO_DIRNAME))){
            mkdir(pathinfo($this->autosave, PATHINFO_DIRNAME));
        }
        if (file_exists($this->autosave)) {
            $content = file_get_contents($this->autosave);
        } 
        if ($content == "")  
        $content = '<p>Just click on the text to start editing. You can insert formula <span class="math-tex">\(x = {-b \pm \sqrt{b^2-4ac} \over 2a}\)</span>or image as well.</p><h2>INPUT</h2><p>M&ocirc; tả input</p><h2>OUTPUT</h2><p>M&ocirc; tả output</p><h2>V&Iacute; DỤ</h2><table border="1" cellpadding="1" cellspacing="1" style="width:100%">	<tbody>		<tr>			<td>Input</td>			<td>Output</td>		</tr>		<tr>			<td>input v&iacute; dụ 1</td>			<td>output v&iacute; dụ 1</td>		</tr>		<tr>			<td>&nbsp;</td>			<td>&nbsp;</td>		</tr>	</tbody></table><p>&nbsp;</p>';        
        file_put_contents($this->autosave, $content);    
        //var_dump($content); die();
        return view('html_edit', ['selected' => 'instructor_panel', 'content' => $content]);
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

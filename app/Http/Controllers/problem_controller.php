<?php

namespace App\Http\Controllers;

use App\Problem;
use App\Setting;
use App\Language;
use App\Tag;
use App\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\UploadedFile;

class problem_controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth'); // phải login
    }
    
    public function index()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);  
        return view('problems.list',['problems'=>Problem::latest()->get()]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);  
        
        return view('problems.create', ['problem'=>NULL,
                                      'all_languages'=>Language::all(),
                                      'tree_dump'=>"not found",
                                      'messages'=>[],
                                      'languages'=>[],
                                      'max_file_uploads'=>1000,    
                                      'all_tags' => Tag::all(),
                                      'tags' => [],
                                  ]);
                                
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->input('tag_id'));
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        
        $validatedData = $request->validate([
            'name' => ['required','max:255'],
        ]);

        $tags = $request->input('tag_id');
        foreach ($tags as $i => $tag) 
        {
            if (Tag::find($tag) == null)
            {
                $new_tag = new Tag;
                $new_tag->text = $tag;
                $new_tag->save();
                $tags[$i]=(string)$new_tag->id;
            }

        }

        $default_language = Language::find(1);
        
        $the_id = $this->new_problem_id();
        $problem = $request->input();
        $problem["allow_practice"] = isset($request["allow_practice"]) ? 1 : 0;
        $p = Problem::create($problem);

        $p->tags()->sync($tags);

        // Processing file 
        $this->_take_test_file_upload($request, $the_id, $messages);  
        
        // handler error
        if ($messages)
            return back()->withInput()->withErrors(["messages"=>$messages]);
        
        
        return redirect()->route('problems.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Problem  $problem
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $result = $this->get_description($id);
        
        $problem = Problem::find($id);
        $problem['has_pdf'] = $result['has_pdf'];
        $problem['description'] = $result['description'];
        return view('problems.show', ['problem'=>$problem,
                                      'all_problems'=>NULL,
                                      'can_submit'=>TRUE,
                                      'assignment'=>NULL,
                                      ]);    
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Problem  $problem
     * @return \Illuminate\Http\Response
     */
    public function edit(Problem $problem)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        $lang_of_problems = $problem->languages;
        $languages = [];
        if ($lang_of_problems != [])
            foreach($lang_of_problems as $lang)
            {
                $languages[$lang->id] = $lang;
            }
        $tags = $problem->tags->keyBy('id');
        return view('problems.create', ['problem'=>$problem,
                                      'all_languages'=>Language::all(),
                                      'messages'=>[],  
                                      'languages'=>$languages,
                                      'tree_dump'=>shell_exec("tree -h " . $this->get_directory_path($problem->id)),  
                                      'max_file_uploads'=>1000,
                                      'all_tags' => Tag::all(),
                                      'tags' => $tags,
                                  ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Problem  $problem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Problem $problem)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);

        $validatedData = $request->validate([
            'name' => ['required','max:255'],
            ]);
        
        $req = $request->input();
        $req["allow_practice"] = isset($request["allow_practice"]) ? 1 : 0;

        $problem->update($req); 
        $problem->tags()->sync($request->input('tag_id'));

        $this->replace_problem($request,$problem->id,$problem);
        $this->_take_test_file_upload($request, $problem->id, $messages);  
        
        if ($messages)
            return back()->withInput()->withErrors(["messages"=>$messages]);
        
        return redirect()->route('problems.index');
    }

    public function _take_test_file_upload(Request $request, $the_id,  &$messages){
        $up_dir = $request->tests_dir;
		$up_zip = $request->tests_zip;
        if (!$up_dir && !$up_zip){
            $messages['type'] = 'Error';
            $messages['text'] = "Notice: You did not upload test case and description. If needed, upload by editing assignment.";
            return ;
        }		
        $assignments_root = Setting::get("assignments_root");
        $problem_dir = $this->get_directory_path($the_id);
     
		if ( ! file_exists($problem_dir) ){
                mkdir($problem_dir, 0700, TRUE); 
        }

        if ($up_zip) {
            //Upload Tests (zip file)
            
            shell_exec('rm -f '.$assignments_root.'/*.zip');
            
			$name_zip = $request->tests_zip->getClientOriginalName();
            $path_zip = $request->tests_zip->storeAs($assignments_root,$name_zip,'my_local');
    
			$this->unload_zip_test_file($request , $assignments_root, $problem_dir, $messages, $name_zip);

		} else {
            if ($up_dir)
            {
                $this->handle_test_dir_upload($request,$assignments_root,$up_dir, $problem_dir, $messages);
            }
		}
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Problem  $problem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = NULL)
    {
        
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);
            
        elseif ($id === NULL)
        {
            $json_result = array('done' => 0, 'message' => 'Input Error');
        }
        else
        {
           
            $problem = Problem::find($id);
            $result['no_of_ass'] = $problem->assignments->count();
            $result['no_of_sub'] = $problem->submissions->count();
            $result['languages'] = $problem->languages;

            if ($problem == NULL)
                $json_result = array('done' => 0, 'message' => 'Not found detailed');
            elseif ($problem['no_of_ass'] != 0 & $problem['no_of_sub'] != 0)
            {
                $json_result = array('done' => 0, 'message' => "Problem already appear in assignments and got some submission should not be delete");
            }
            else
            {
                $this->delete_problem($id);
                $json_result = array('done' => 1);
            }
            // dd($id);

        }
        
		header('Content-Type: application/json; charset=utf-8');  
        return ($json_result);
    }

    public function save_problem_description($problem_id, $text, $type = 'html')
	{
		$problem_dir = $this->get_directory_path($problem_id);
		if (file_put_contents("$problem_dir/desc.html", $text) ) 
			return true;
		else return false;
    }
    
    public function edit_description(Request $request, $id){
		if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);

        // $request->validate([
        //     'content'=>['required','text']
        // ]); 

		if ($this->save_problem_description($id, $request->content)){
            echo "success";
                return;
		}
        else 
            echo "error";
    }
    
    private function unload_zip_test_file(Request $request, $assignments_root, $problem_dir, &$messages, $name_zip){
        
        // Create a temp directory
		$tmp_dir_name = "shj_tmp_directory";
        $tmp_dir = "$assignments_root/$tmp_dir_name";
		shell_exec("rm -rf $tmp_dir; mkdir $tmp_dir;");
       
        // get new name
		$rename_inputoutput = $request->rename_zip;

        // extract file 
        
        shell_exec("cd $assignments_root; unzip $name_zip -d $tmp_dir");

		// Remove the zip file
		shell_exec("cd $assignments_root; rm -rf $name_zip");
        
        $a=1;
        if ($a==1)
		{
			$this->clean_up_old_problem_dir($problem_dir);

			if (glob("$tmp_dir/*.pdf"))
				shell_exec("cd $problem_dir; rm -f *.pdf");

			shell_exec("cp -R $tmp_dir/* $problem_dir;");
			// $messages[] = array(
			// 	'type' => 'success',
			// 	'text' => 'Tests (zip file) extracted successfully.'
			// );
			$in = glob("$problem_dir/in/*");
            $out = glob("$problem_dir/out/*");
            
			if ($in){
				//rename input and output file base on file name order
				if ($rename_inputoutput){
					if (count($in) != count($out)){
						$messages['type'] = 'Error';
						$messages['text'] = 'The zip contain mismatch number of input and output files: ' . count($in) . ' input files vs ' . count($out) . ' output files';					}
					else {
						for($i = 1; $i <= count($in); $i++){
							rename($in[$i-1], "$problem_dir/in/input$i.txt");
							rename($out[$i-1], "$problem_dir/out/output$i.txt");
						}
					}
				} else {
                    //Check input and output file but won't rename
                    // var_dump($problem_dir);die();$problem_dir."/out/output$i.txt"
					for($i = 0; $i < count($in); $i++){
                        $real_id = $i+1;
						if (!in_array($problem_dir."/in/input$real_id.txt",$in)){
                            $messages['type']= 'Error';
                            $messages['text'] = "A file name input$real_id.txt seem to be missing in your folder";
						} else {
							if (!in_array($problem_dir."/out/output$real_id.txt",$out)){
                                $messages['type'] = 'Error';
                                $messages['text'] = "A file name output$real_id.txt seem to be missing in your folder";
							}
						}
					}
				}
			}
		}
		else
		{
			$messages[] = array(
				'type' => 'error',
				'text' => 'Error: Error extracting zip archive.'
			);
		}

		// Remove temp directory
		shell_exec("rm -rf $tmp_dir");
	}
    public function get_directory_path($id = NULL){
        if ($id === NULL) return NULL;
        
		$assignments_root = Setting::get("assignments_root");
        
        $problem_dir = $assignments_root . "/problems/".$id;
       
        return $problem_dir;
    }

    private function clean_up_old_problem_dir($problem_dir){
		$remove = 
		" rm -rf $problem_dir/in $problem_dir/out $problem_dir/tester*"
			."  $problem_dir/template.* "
			."  $problem_dir/desc.*  $problem_dir/*.pdf; done";
		//echo "cp -R $tmp_dir/* $problem_dir;";			
		//echo $remove; die();			
		shell_exec($remove); 

		mkdir("$problem_dir/in", 0700, TRUE);
		mkdir("$problem_dir/out", 0700, TRUE);
			
	}

    public function get_description($id = NULL){
        $problem_dir = $this->get_directory_path($id);
        
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
    
    public function delete_problem($id){
        
        $cmd = 'rm -rf '.$this->get_directory_path($id);
      
		 // If you want to set transaction time, you can append the new argument in the transaction function
        // dd($id);
        DB::beginTransaction();  

        $problem = Problem::find($id);
        Problem::destroy($id);  
        
        $problem->languages()->detach();
        $problem->assignments()->detach();
        
        $problem->submissions->each(function($item,$key){
            Submission::destroy($item);
        });

        $problem->tags()->detach();
            
        DB::commit();
        
        // Make the path to prepare to delete problem
        $cmd = 'rm -rf '.$this->get_directory_path($id);
        
        // Delete assignment's folder (all test cases and submitted codes)
        
        shell_exec($cmd);
    }

  
    /** Dowload file pdf  */
    public function pdf($problem_id)
	{
        // Find pdf file
		if ($problem_id === NULL)
            abort(404);
        else
            $pattern = $this->get_directory_path($problem_id)."/*.pdf";
			
        $pdf_files = glob($pattern);
        $pdf_files = implode("|",$pdf_files);
        
		if ( ! $pdf_files )
            abort(404,"File not found");

		// Download the file to browser
        return response()->download($pdf_files);
    
    }
    
    public function new_problem_id(){
		$max = Problem::count()+1 ;
		$assignments_root = Setting::get("assignments_root");
		while (file_exists($assignments_root.'/problems/'.$max)){
			$max++;
        }
		return $max;
    }

    public function get_template_path($problem_id = NULL){
		$pattern1 = rtrim($this->get_directory_path($problem_id)
		."/template.public.cpp");

		$template_file = glob($pattern1);
		if ( ! $template_file ){
			$pattern = rtrim($this->get_directory_path($problem_id)
						."/template.cpp");

			$template_file = glob($pattern);
		}
		return $template_file;
	}

    // public function test()
    // {
    //     $data = Problem::problem_info_detailed(1);
    //     return view('problems.test',['data'=>$data]);
    // }

    private function handle_test_dir_upload(Request $request,$assignments_root,$up_dir, $problem_dir, &$messages)
    {
        
        $tmp_dir_name = "shj_tmp_directory";
        $tmp_dir = "$assignments_root/$tmp_dir_name";
        shell_exec("rm -rf $tmp_dir; mkdir $tmp_dir;");
        
        
        foreach($request->tests_dir as $item)
        {
            $item->storeAs($tmp_dir,$item->getClientOriginalName(),'my_local');
        }

        // path data
        $data = glob("$tmp_dir/*");

        //
        if (!in_array($tmp_dir.'/desc.html',$data))
        {
            $messages['type'] = 'Error';
            $messages['text'] = "Your test folder doesn't have desc.html file for problem description";
        }

        for($i = 0; $i<count($data);$i++)
        {
            // var_dump($data[$i]);
            $path = explode("string",$data[$i]);
            $name = explode("/",$data[$i]);
            $name_with_extension = explode(".",end($name));
            $prefix_name = $name_with_extension[0];
            
            if (substr($prefix_name, 0, 5) == 'input') {
				$in[end($name)] = $data[$i];
			} else if (substr($prefix_name, 0, 6) == 'output'){
				$out[end($name)] = $data[$i];
			} else {
				$files[end($name)] = $data[$i];
			}
        }
        
        if (!isset($files['desc.html'])){
			$messages[] = array('type' => 'error', 'text' => "Your test folder doesn't have desc.html file for problem description");
        }
    
        for($i = 1; $i < count($in); $i++){
			if (!isset($in["input$i.txt"])){
                $messages['type'] = 'Error';
                $messages['text'] = "A file name input$i.txt seem to be missing in your folder";
			} else {
				if (!isset($out["output$i.txt"])){
                    $messages['type'] = 'Error';
                    $messages['text'] = "A file name output$i.txt seem to be missing in your folder";
				}
			}
        }
        
        $this->clean_up_old_problem_dir($problem_dir);

		foreach($in as $name => $tmp_name ){
            rename($tmp_name, "$problem_dir/in/$name");
        }
        
		foreach($out as $name => $tmp_name ){
			rename($tmp_name, "$problem_dir/out/$name");
        }
        
		foreach($files as $name => $tmp_name ){
			rename($tmp_name, "$problem_dir/$name");
        }
    } 
    
    public function replace_problem(Request $request, $id , Problem $problem)
    {
        DB::beginTransaction(); 

        $time_limit = $request->time_limit;
		$memory_limit = $request->memory_limit;
        $enable = $request->enable;
        
        $problem->languages()->detach();
        
        for($i=0;$i<count($enable);$i++){
            if($enable[$i]){ 
                $problem->languages()->attach($request->language_id[$i],
                        [
                            'time_limit' => $time_limit[$i],
                            'memory_limit' => $memory_limit[$i],
                        ]
                    );
			    }
        }

        DB::commit();
    }

}

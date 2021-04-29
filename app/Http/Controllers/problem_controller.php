<?php

namespace App\Http\Controllers;

use App\Problem;
use App\Setting;
use App\Language;
use App\Tag;
use App\Submission;
use App\Assignment;
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
    
    public function index(Request $request)
    {
        // DB::enableQueryLog();
        // if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
        //     abort(404);  
        if (Auth::user()->role->name == 'admin') $all_problem = Problem::latest();
        else $all_problem = Problem::available(Auth::user()->id)->latest();

        if ($request->get('search') != ""){
            $all_problem->where('name', 'like', "%".trim($request->get('search'))."%");
        }

        $all_problem = $all_problem->with('assignments', 'languages')->paginate(Setting::get('results_per_page_all'));
        $all_problem->appends(['search' => $request->get('search')]);
        
        $a  =  $all_problem->pluck('id');

        $total_subs = Submission::groupBy('problem_id')->whereIn('problem_id', $a)->select('problem_id', DB::raw('count(*) as total_sub'))->get()->keyBy('problem_id');
        $ac_subs = Submission::groupBy('problem_id')->whereIn('problem_id', $a)->where('pre_score', 10000)->select('problem_id', DB::raw('count(*) as total_sub'))->get()->keyBy('problem_id');

        foreach ($all_problem as $p){
            $p->total_submit = $total_subs[$p->id]->total_sub ?? 0;
            $p->accepted_submit = $ac_subs[$p->id]->total_sub ?? 0;
            $p->ratio = round($p->accepted_submit / max($p->total_submit,1), 2)*100;
        }
        // dd(DB::getQueryLog());
        return view('problems.list',['problems'=>$all_problem]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);  
        
        return view('problems.create', ['problem'=>NULL,
                                      'all_languages'=>Language::orderBy('sorting')->get(),
                                      'tree_dump'=>"not found",
                                      'messages'=>[],
                                      'languages'=>[],
                                      'max_file_uploads'=> ini_get('max_file_uploads'),    
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
        if ($tags != null)
        {
            foreach ($tags as $i => $tag) 
            {
                if (Tag::find($tag) == null)
                    if (Tag::where('text', $tag)->first() == [])
                    {
                        $new_tag = new Tag;
                        $new_tag->text = $tag;
                        $new_tag->save();
                        $tags[$i]=(string)$new_tag->id;
                    }
                    else
                    {
                         \array_splice($tags, $i, 1);
                    }
            }
        }
        
        $langs = [];
        foreach ($request->input('enable') as $i => $lang)
            if ($lang == 1)
                $langs += [$request->input('language_id')[$i] => ['time_limit' => $request->input('time_limit')[$i], 'memory_limit' => $request->input('memory_limit')[$i]]];

        //$default_language = Language::find(1);

        // $the_id = Problem::max('id') + 1;
        $problem = $request->input();
        // $problem['id'] = $the_id;
        $problem['user_id'] = Auth::user()->id;
        $problem["allow_practice"] = isset($request["allow_practice"]) ? 1 : 0;
        $problem["sharable"] = isset($request["sharable"]) ? 1 : 0;
        $p = Problem::create($problem);
        if ($tags != null)
        {
            $p->tags()->sync($tags);
        }

        $p->languages()->sync($langs);

        // Processing file 
        $this->_take_test_file_upload($request, $p->id, $messages);  

        return redirect()->route('problems.index')->withInput()->withErrors(["messages"=>$messages]);
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
        $problem['has_template'] = $result['has_template'];
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
        if ( ! in_array( Auth::user()->role->name, ['admin']) )
        {
            //Admin can always edit
            if ($problem->user->id != Auth::user()->id){
                //Others can only edit problems they own
                abort(404); 
            } 
        }
        $lang_of_problems = $problem->languages->keyBy('id');

        $tags = $problem->tags->keyBy('id');
        return view('problems.create', ['problem'=>$problem,
                                      'all_languages'=>Language::orderBy('sorting')->get(),
                                      'messages'=>[],  
                                      'languages'=>$lang_of_problems,
                                      'tree_dump'=>shell_exec("tree -h " . $this->get_directory_path($problem->id)),  
                                      'max_file_uploads'=> ini_get('max_file_uploads'),
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
        if ( ! in_array( Auth::user()->role->name, ['admin']) )
        {
            //Admin can always edit
            if ($problem->user->id != Auth::user()->id){
                //Others can only edit problems they own
                abort(404); 
            } 
        }

        $validatedData = $request->validate([
            'name' => ['required','max:255'],
            'editorial' => 'nullable|url'
            ]);
        
        $req = $request->input();
        $req["allow_practice"] = isset($request["allow_practice"]) ? 1 : 0;
        $req["sharable"] = isset($request["sharable"]) ? 1 : 0;

        $problem->update($req); 

        $tags = $request->input('tag_id');

        if ($tags != null)
        {
            foreach ($tags as $i => $tag) 
            {
                if (Tag::find($tag) == null)
                    if (Tag::where('text', $tag)->first() == [])
                    {
                        $new_tag = new Tag;
                        $new_tag->text = $tag;
                        $new_tag->save();
                        $tags[$i]=(string)$new_tag->id;
                    }
                    else
                    {
                        array_splice($tags, $i, 1);
                    }
            }
        } 

        $problem->tags()->sync($tags);

        $this->replace_problem($request,$problem->id,$problem);
        $this->_take_test_file_upload($request, $problem->id, $messages);  
        
        if ($messages)
            return back()->withInput()->withErrors(["messages"=>$messages]);
        
        return redirect()->route('problems.index');
    }

    private function _take_test_file_upload(Request $request, $the_id,  &$messages){
        $up_dir = $request->tests_dir;
        $up_zip = $request->tests_zip;
        if (!$up_dir && !$up_zip){
            //             $messages = "Notice: You did not upload test case and description. If needed, upload by editing assignment.";
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
            
            $name_zip = ($request->tests_zip->getClientOriginalName());


            $path_zip = $request->tests_zip->storeAs('',$name_zip,'assignment_root');
            
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

        }
        
        header('Content-Type: application/json; charset=utf-8');  
        return ($json_result);
    }

    private function save_problem_description($problem_id, $text, $type = 'html')
    {
        $problem_dir = $this->get_directory_path($problem_id);
        if (file_put_contents("$problem_dir/desc.html", $text) ) 
            return true;
        else return false;
    }
    
    public function edit_description(Request $request, $id){
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);

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
        // dd("rm -rf $tmp_dir; mkdir $tmp_dir;");
        shell_exec("rm -rf $tmp_dir; mkdir $tmp_dir;");
       
        // get new name
        $rename_inputoutput = $request->rename_zip;

        // extract file 
        
        shell_exec("cd $assignments_root; unzip ". escapeshellarg($name_zip) . " -d $tmp_dir");

        // Remove the zip file
        shell_exec("cd $assignments_root; rm -rf " . escapeshellarg( $name_zip) );
        
        $a=1;
        if ($a==1)
        {
            $this->clean_up_old_problem_dir($problem_dir);

            if (glob("$tmp_dir/*.pdf"))
                shell_exec("cd $problem_dir; rm -f *.pdf");

            shell_exec("cp -R $tmp_dir/* $problem_dir;");

            $in = glob("$problem_dir/in/*");
            $out = glob("$problem_dir/out/*");
            
            if ($in){
                //rename input and output file base on file name order
                if ($rename_inputoutput){
                    if (count($in) != count($out)){
                        $messages[] = 'The zip contain mismatch number of input and output files: ' . count($in) . ' input files vs ' . count($out) . ' output files';                  }
                    else {
                        shell_exec("cd $problem_dir; rm -f in_old out_old");
                        rename("$problem_dir/in", "$problem_dir/in_old");
                        rename("$problem_dir/out", "$problem_dir/out_old");
                        shell_exec("cd $problem_dir; mkdir in out");
                        $in = glob("$problem_dir/in_old/*");
                        $out = glob("$problem_dir/out_old/*");
                        // dd($out);
                        for($i = 1; $i <= count($in); $i++){
                            // var_dump([$in[$i-1],"$problem_dir/in/input$i.txt"] ); 
                            copy($in[$i-1], "$problem_dir/in/input$i.txt");
                            copy($out[$i-1], "$problem_dir/out/output$i.txt");
                        }
                        shell_exec("cd $problem_dir; rm -f in_old out_old");
                        shell_exec("rm -rf $problem_dir/in_old");            
                        shell_exec("rm -rf $problem_dir/out_old");            
                        // dd($in);
                    }
                } else {
                    //Check input and output file but won't rename
                    // var_dump($problem_dir);die();$problem_dir."/out/output$i.txt"
                    for($i = 0; $i < count($in); $i++){
                        $real_id = $i+1;
                        if (!in_array($problem_dir."/in/input$real_id.txt",$in)){
                                                        $messages[]= "A file name input$real_id.txt seem to be missing in your folder";
                        } else {
                            if (!in_array($problem_dir."/out/output$real_id.txt",$out)){
                                $messages[] = "A file name output$real_id.txt seem to be missing in your folder";
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $messages[] = 'Error: Error extracting zip archive.';
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
        " rm -rf $problem_dir/*";
        // " rm -rf $problem_dir/in $problem_dir/out $problem_dir/tester*"
        //     ."  $problem_dir/template.* "
        //     ."  $problem_dir/desc.*  $problem_dir/*.pdf; done";
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
        DB::beginTransaction();  

        Submission::where('problem_id', $id)->delete();


        $problem = Problem::find($id);
        Problem::destroy($id);  
        
        $problem->languages()->detach();
        $problem->assignments()->detach();

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

    public function template($problem_id, $assignment_id)
    {
        if ($assignment_id == 'null')
            $assignment_id = NULL;
        if ($assignment_id == NULL && !in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']))
            abort(403, "Only admin can view template without assignment");
        if ($assignment_id != NULL && Assignment::find($assignment_id)->problems->find($problem_id) == null)
            abort(404);
        if ($assignment_id == NULL && Problem::find($problem_id) == null)
            abort(404); 

        $template_file = $this->get_template_path($problem_id);
        if(!$template_file)
            abort(404, "File note found");

        // Download the file to browser
        return response()->download($template_file[0]);
    }


    public function downloadtestsdesc($problem_id)
    {
        $a =Problem::find($problem_id); 
        
        if ($a == null) abort(404);
        if ( ! in_array( Auth::user()->role->name, ['admin']) )
        {   
            if (! $a->sharable && $a->user->id != Auth::user()->id) abort(403, 'you can only download sharable problems and problems that you upload');
        }
        
        $assignments_root = Setting::get("assignments_root");
        $zipFile = $assignments_root . "/problem" . (string)$problem_id . "_tests_and_descriptions_" . (string)date('Y-m-d_H-i') . ".zip";
        $pathdir = $assignments_root . '/problems/' . (string)$problem_id . '/';
        
        exec("cd $pathdir && zip -r $zipFile *");
        return response()->download($zipFile)->deleteFileAfterSend();
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
        // dd("rm -rf $tmp_dir; mkdir $tmp_dir;");
        
        
        foreach($request->tests_dir as $item)
        {
            $item->storeAs($tmp_dir_name,$item->getClientOriginalName(),'assignment_root');
        }

        // path data
        $data = glob("$tmp_dir/*");

        //
        if (!in_array($tmp_dir.'/desc.html',$data))
        {
            $messages[] = "Your test folder doesn't have desc.html file for problem description";
        }
        $in = $out = $files = array();
        // dd($data);
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
            $messages[] = "Your test folder doesn't have desc.html file for problem description";
        }

    
        for($i = 1; $i < count($in); $i++){
            if (!isset($in["input$i.txt"])){
                $messages[] = "A file name input$i.txt seem to be missing in your folder";
            } else {
                if (!isset($out["output$i.txt"])){
                    $messages[] = "A file name output$i.txt seem to be missing in your folder";
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
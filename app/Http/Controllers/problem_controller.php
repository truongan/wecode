<?php

namespace App\Http\Controllers;

use App\Problem;
use App\Setting;
use App\Language;
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
    public function index()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);  
        return view('problems.list',['problems'=>Problem::all()]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('problems.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        var_dump($request['tests_zip']);
        die();
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Problem  $problem
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);

		$data=[
			'can_submit' => TRUE,
            'assignment' => NULL,
            'error'=>'none'
        ];
        $re = $this->get_description($id);

        return view('problems.show', ['can_submit' => TRUE,
                                      'assignment' => NULL,
                                      'error'=>'none',
                                      'description'=>$this->get_description($id),
                                      'problem' => Problem::problem_info($id),
                                      'all_problems' =>NULL,
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
        return view('problems.edit', ['problem'=>$problem,'languages'=>Language::all()]);
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
        $validatedData = $request->validate([
            'name' => ['required','max:255'],
            'diff_cmd' => ['required','max:200'],
            'admin_note'=>['required','max:255']
        ]);
        DB::beginTransaction(); 
        $id = $problem->id ? $problem->id : $this->new_problem_id();
        $problem->update($request->input()); 
        DB::table('language_problem')->where('problem_id','=',$id)->delete();
        
        $time_limit = $request->time_limit;
		$memory_limit = $request->memory_limit;
        $enable = $request->enable;
        //Now add new problems:
        for($i=0;$i<count($enable);$i++){
            if($enable[$i]){
				DB::table('language_problem')->insert([
					'language_id' => $request->language_update[$i],
					'problem_id' => $problem->id,
					'time_limit' => $time_limit[$i],
					'memory_limit' => $memory_limit[$i],
                ]);
			}
        }
        $problem_dir = $this->get_directory_path($id);
        
        // if ($request->hasFile('pdf_file')) {
        //     $this->_take_test_file_upload($id);   
        // }

        DB::commit();

        return redirect()->route('problems.index');
    }

    public function _take_test_file_upload($the_id){
        // Tôi Rất mệt ~~ 
		// $assignments_root = Setting::get("assignments_root");
        // $problem_dir = $this->get_directory_path($the_id);
    
		// // Create assignment directory
		// if ( ! file_exists($problem_dir) )
		// 	mkdir($problem_dir, 0700, TRUE);
        // var_dump(1);
        // die();
		// $this->load->library('upload');
		// $up_dir = $_FILES['tests_dir'];
		// $up_zip = $_FILES['tests_zip'];

		// if ( $up_dir['error'][0] === UPLOAD_ERR_NO_FILE 
		// 	&& $up_zip['error'] === UPLOAD_ERR_NO_FILE 
		// ){
		// 	$messages[] = array(
		// 		'type' => 'notice',
		// 		'text' => "Notice: You did not upload test case and description. If needed, upload by editing assignment."
		// 	);
		// 	return;
		// }

		// if ($up_dir['error'][0] === UPLOAD_ERR_NO_FILE ) {
			// Upload Tests (zip file)
			// shell_exec('rm -f '.$assignments_root.'/*.zip');
			// $config = array(
			// 	'upload_path' => $assignments_root,
			// 	'allowed_types' => 'zip',
			// );
			// $this->upload->initialize($config);
			// $zip_uploaded = $this->upload->do_upload('tests_zip');
			// $u_data = $this->upload->data();
			
			// if ( ! $zip_uploaded )
			// 	$messages[] = array(
			// 		'type' => 'error',
			// 		'text' => "Error: Error uploading tests zip file: ".$this->upload->display_errors('', '')
			// 	);
			// else
			// 	$messages[] = array(
			// 		'type' => 'success',
			// 		'text' => "Tests (zip file) uploaded successfully."
			// 	);

		// 	if ($zip_uploaded) $this->unload_zip_test_file($assignments_root, $problem_dir, $u_data, $messages);

		// } else {
		// 	return $this->handle_test_dir_upload($up_dir, $problem_dir, $messages);
		// }
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
            $problem = Problem::problem_info_detailed($id);
         
            if ($problem == NULL)
                $json_result = array('message' => 'Not found detailed');
            elseif ($problem['no_of_ass'] != 0 & $problem['no_of_sub'] != 0)
            {
                $json_result = array("message" => "Problem already appear in assignments and got some submission should not be delete");
            }
            $this->delete_problem($id);
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
    
    public function edit_description($problem_id){
		if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);
		var_dump("ok");die();
		// $this->load->library('form_validation');
		// $this->form_validation->set_rules('content', 'text' ,'required'); /* todo: xss clean */
		// if ($this->form_validation->run())
		// {
		// 	if ($this->problem_files_model->save_problem_description($problem_id, $this->input->post('content'))){
		// 		echo "success";
		// 		return ;
		// 	}
		// 	else show_error("Error saving", 501);
		// } else {
		// 	show_error(validation_errors(), 501);
        // }
        return view('problems.edit');
	}
    private function unload_zip_test_file($assignments_root, $problem_dir, $u_data, &$messages){
        
        // // Create a temp directory
		// $tmp_dir_name = "shj_tmp_directory";
		// $tmp_dir = "$assignments_root/$tmp_dir_name";
		// shell_exec("rm -rf $tmp_dir; mkdir $tmp_dir;");

		// $rename_inputoutput = $request->rename_zip;

		// // Extract new test cases and descriptions in temp directory
		// $this->load->library('unzip');
		// // $this->unzip->allow(array('txt', 'cpp', 'html', 'md', 'pdf'));
		// $extract_result = $this->unzip->extract($u_data['full_path'], $tmp_dir);

		// // Remove the zip file
		// unlink($u_data['full_path']);

		// if ( $extract_result )
		// {
		// 	$this->clean_up_old_problem_dir($problem_dir);

		// 	if (glob("$tmp_dir/*.pdf"))
		// 		shell_exec("cd $problem_dir; rm -f *.pdf");

		// 	shell_exec("cp -R $tmp_dir/* $problem_dir;");
		// 	$messages[] = array(
		// 		'type' => 'success',
		// 		'text' => 'Tests (zip file) extracted successfully.'
		// 	);
		// 	$in = glob("$problem_dir/in/*");
		// 	$out = glob("$problem_dir/out/*");

		// 	if ($in){
		// 		//rename input and output file base on file name order
		// 		if ($rename_inputoutput){
		// 			if (count($in) != count($out)){
		// 				$messages[] = array(
		// 					'type' => 'error',
		// 					'text' => 'The zip contain mismatch number of input and output files: ' . count($in) . ' input files vs ' . count($out) . ' output files'
		// 				);
		// 			}
		// 			else {
		// 				for($i = 1; $i <= count($in); $i++){
		// 					rename($in[$i-1], "$problem_dir/in/input$i.txt");
		// 					rename($out[$i-1], "$problem_dir/out/output$i.txt");
		// 				}
		// 			}
		// 		} else {
		// 			//Check input and output file but won't rename
		// 			for($i = 1; $i < count($in); $i++){
		// 				if (!isset($in["input$i.txt"])){
		// 					$messages[] = array('type' => 'error', 'text' => "A file name input$i.txt seem to be missing in your folder");
		// 				} else {
		// 					if (!isset($out["output$i.txt"])){
		// 						$messages[] = array('type' => 'error', 'text' => "A file name output$i.txt seem to be missing in your folder");
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// }
		// else
		// {
		// 	$messages[] = array(
		// 		'type' => 'error',
		// 		'text' => 'Error: Error extracting zip archive.'
		// 	);
		// 	foreach($this->unzip->errors_array() as $msg)
		// 		$messages[] = array(
		// 			'type' => 'error',
		// 			'text' => " Zip Extraction Error: ".$msg
		// 		);
		// }

		// // Remove temp directory
		// shell_exec("rm -rf $tmp_dir");
	}
    public function get_directory_path($id = NULL){
        if ($id === NULL) return NULL;
        
		$assignments_root = Setting::get("assignments_root");
       
        $problem_dir = $assignments_root . "/problems/".$id;
       
        return $problem_dir;
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
        Problem::destroy($id);  
        DB::table('language_problem')->where('problem_id','=',$id)->delete();
        DB::table('assignment_problem')->where('problem_id','=',$id)->delete();
        DB::table('submissions')->where('problem_id','=',$id)->delete();
            
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
		$max = $max = DB::table('problems')->count()+1 ;

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

    public function test()
    {
        $data = Problem::problem_info_detailed(1);
        return view('problems.test',['data'=>$data]);
    }


}

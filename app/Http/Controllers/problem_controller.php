<?php

namespace App\Http\Controllers;

use App\Problem;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function show($id)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        if ($id)
        {
            $data = DB::table('problems')->find($id);
            if ($data)
                return view('problems.show',["problem" => $data]);
        }
           
        return view('problems.list',['problems'=>Problem::all()]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Problem  $problem
     * @return \Illuminate\Http\Response
     */
    public function edit(Problem $id)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);
        if (!$request->has($request['file']))
        {
            return view('problems.list',['problems'=>Problem::all()]); 
        }
        return rediect('problems');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Problem  $problem
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Problem $id)
    // {
    //     if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
    //         abort(404);
    //     if ($id!=NULL)
    //         Problem::destroy($id);
    //     return view('problems.list',['problems'=>Problem::all()]);  
    // }
    
    // private function unload_zip_test_file($assignments_root, $problem_dir, $u_data){
	// 	// Create a temp directory
	// 	$tmp_dir_name = "shj_tmp_directory";
	// 	$tmp_dir = "$assignments_root/$tmp_dir_name";
	// 	shell_exec("rm -rf $tmp_dir; mkdir $tmp_dir;");

	// 	// Extract new test cases and descriptions in temp directory
	// 	$this->load->library('unzip');
	// 	$this->unzip->allow(array('txt', 'cpp', 'html', 'md', 'pdf'));
	// 	$extract_result = $this->unzip->extract($u_data['full_path'], $tmp_dir);

	// 	// Remove the zip file
	// 	unlink($u_data['full_path']);

	// 	if ( $extract_result )
	// 	{
	// 		$this->clean_up_old_problem_dir($problem_dir);

	// 		if (glob("$tmp_dir/*.pdf"))
	// 			shell_exec("cd $problem_dir; rm -f *.pdf");
	// 		// Copy new test cases from temp dir
	// 		// echo $tmp_dir . "<br/>";
	// 		// echo $problem_dir . "<br/>";
	// 		// echo shell_exec("ls $tmp_dir/*");
	// 		// echo "cp -R $tmp_dir/* $problem_dir;";
	// 		//die();
	// 		shell_exec("cp -R $tmp_dir/* $problem_dir;");
	// 		$this->messages[] = array(
	// 			'type' => 'success',
	// 			'text' => 'Tests (zip file) extracted successfully.'
	// 		);
	// 	}
	// 	else
	// 	{
	// 		$this->messages[] = array(
	// 			'type' => 'error',
	// 			'text' => 'Error: Error extracting zip archive.'
	// 		);
	// 		foreach($this->unzip->errors_array() as $msg)
	// 			$this->messages[] = array(
	// 				'type' => 'error',
	// 				'text' => " Zip Extraction Error: ".$msg
	// 			);
	// 	}

	// 	// Remove temp directory
	// 	shell_exec("rm -rf $tmp_dir");
    // }
    public function get_directory_path($id = NULL){
		if ($id === NULL) return NULL;
		$assignments_root = rtrim(Setting::get('assignments_root'),'/');
		$problem_dir = $assignments_root . "/problems/".$id;
		return $problem_dir;
    }

    public function get_description($id = NULL){
		$problem_dir = $this->get_directory_path($id);
		$result =  array(
			'description' => '<p>Description not found</p>',
			'has_pdf' => glob("$problem_dir/*.pdf") != FALSE
			,'has_template' => glob("$problem_dir/template.cpp") != FALSE
		);
		
		$path = "$problem_dir/desc.html";

		if (file_exists($path))
			$result['description'] = file_get_contents($path);

		return $result;
    }
    
    public function delete_problem($id){
		$cmd = 'rm -rf '.$this->get_directory_path($id);
		//var_dump($cmd);die();
		DB::beginTransaction();
		// Phase 1: Delete this assignment and its submissions from database
		Problem::destroy($id);
		$this->db->delete('problem_language', array('problem_id'=>$id));
		$this->db->delete('problem_assignment', array('problem_id'=>$id));
		DB::table('submissions')->where('problem_id',$id);

		$this->db->trans_complete();

		if ($this->db->trans_status())
		{
			// Phase 2: Delete assignment's folder (all test cases and submitted codes)
			$cmd = 'rm -rf '.$this->get_directory_path($id);

			shell_exec($cmd);
		}
	}

}

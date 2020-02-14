<?php

namespace App\Http\Controllers;

use App\Problem;
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
        
        var_dump($request);
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
}

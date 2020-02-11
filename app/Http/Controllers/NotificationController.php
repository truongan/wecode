<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notification;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('notifications.list', ['notifications'=>Notification::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        return view('notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);

		// $this->form_validation->set_rules('title', 'title', 'trim');
		// $this->form_validation->set_rules('text', 'text', ''); /* todo: xss clean */
        // var_dump($request['text']);die();
		// if($this->form_validation->run()){
            Notification::create($request->input());
		    return redirect('notifications');
		// }        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        $notification = Notification::find($id);
        return view('notifications.edit', ['notification'=>$notification]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);

		// $this->form_validation->set_rules('title', 'title', 'trim');
		// $this->form_validation->set_rules('text', 'text', ''); /* todo: xss clean */

		// if($this->form_validation->run()){
            $notification = Notification::find($id);
            $notification->title = $request['title'];
            $notification->text = $request['text'];
            $notification->save();
            return redirect('notifications');
		// }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // if ( ! $this->input->is_ajax_request() )
        // 	show_404();
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
			$json_result = array('done' => 0, 'message' => 'Permission Denied');
		elseif ($id === NULL)
			$json_result = array('done' => 0, 'message' => 'Input Error');
		else
		{
			Notification::destroy($id);
			$json_result = array('done' => 1);
		}
        header('Content-Type: application/json; charset=utf-8');
		return ($json_result);
    }
}

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
        return view('notifications.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

		// if($this->form_validation->run()){
		    if ($request['id'] === NULL)
            	Notification::create($request->input());
        // var_dump($request->input());die();
		// 	else
		// 		$this->notifications_model->update_notification($this->input->post('id'), $this->input->post('title'), $this->input->post('text'));
		// 	redirect('notifications');
		// }

		// $data = array(
		// 	'all_assignments' => $this->assignment_model->all_assignments(),
		// 	'notif_edit' => $this->notif_edit
		// );

		// if ($this->notif_edit !== FALSE)
		// 	$data['title'] = 'Edit Notification';


        // $this->twig->display('pages/admin/add_notification.twig', $data);
        return view('notifications.list');
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

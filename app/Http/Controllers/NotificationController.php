<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request, Notification $notifications)
    {
        $notifications->title = $request->title;
        $notifications->text = $request->text;
        $notifications->dataTime = dataTime('time');
        $notifications->timestamps();
        $notifications->save();
        return view('notifications', ['selected' => 'notifications']);
    }

    public function add(Request $request)
    {
        $notifications = new Notification;
        $notifications->title = $request->title;
        $notifications->text = $request->text;
        $notifications->dataTime = dataTime('time');
        $notifications->timestamps();
        $notifications->save();
        return view('notifications', ['selected' => 'notifications']);
    }

    public function destroy(Notification $notification)
    {
        $notification = Notification::find($notification->id);
        $notification->delete();
        return view('notifications', ['selected' => 'notifications']);
    }

    public function get(Request $request)
    {
        $notification = Notifications::find($request->id);
        return view('notifications', ['selected' => 'notifications']);
    }
    
    public function index()
    {
        return view('notifications', ['selected' => 'notifications']);
    }
}

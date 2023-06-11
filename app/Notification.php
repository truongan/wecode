<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Notification extends Model
{
    //
    protected $fillable = ['title', 'text', 'author', 'description', 'last_author', 'recipent_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'author');
    }
    
    public function last_user()
    {
        return $this->belongsTo('App\User', 'last_author');
    }
    
    public static function whereUser($user){
        $list_ids = [$user->id, 0];
        if (in_array( $user->role->name, ['admin'])){
            array_push($list_ids, -1);
        }
        // dd($list_ids);
        $notification = Notification::whereIn('recipent_id', $list_ids)
                            ->orWhere('recipent_id', 0)
                            ->orWhere('author', $user->id)
                            ;


        if (in_array( $user->role->name, ['admin'])){
            $notificaiton = $notification->orWhereNull('recipent_id');
        }
        return $notification;
    }

}

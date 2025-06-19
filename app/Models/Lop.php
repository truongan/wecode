<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    //
    protected $fillable = ['name', 'open'];
    function users(){
        return $this->belongsToMany('App\User');
    }
    function assignments(){
        return $this->belongsToMany('App\Assignment');
    }

    static function available($user_id){

        return Lop::where(['open'=>1])
            ->orWhereHas(
                'users',        
                function($q) use( $user_id)
                { 
                    $q->where(['users.id'=>$user_id]);
                } 
            );
    }
}

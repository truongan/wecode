<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    //
    protected $fillable = ['name', 'open'];
    function users(){
        return $this->belongsToMany('App\Models\User');
    }
    function assignments(){
        return $this->belongsToMany('App\Models\Assignment');
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

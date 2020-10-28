<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;
use Hash;


class User extends Authenticatable
{
    use Notifiable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'display_name', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_time' => 'datetime:Y-m-d\TH:i:sP'
    ];
    protected $dates = ['first_login_time', 'last_login_time'];
    // protected $dateFormat = 'Y-m-d\TH:i:sP';


    public function role(){
        return $this->belongsTo('App\Role');
	}
	
    function submissions()
    {
        return $this->hasMany('App\Submission');
    }

	function lops(){
		return $this->belongsToMany('App\Lop');
    }
    
    function selected_assignment(){
        return $this->belongsTo('App\Assignment', 'selected_assignment_id');
    }
}

//---------------------------------------------------------------------------------------------------------

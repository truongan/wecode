<?php

namespace App\Models;



// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Hash;

use Role;


class User extends Authenticatable
{
    use Notifiable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'display_name', 'role_id', 'trial_time'
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
        'email_verified_at' => 'datetime'
        ,'first_login_time' => 'datetime:Y-m-d\TH:i:sP'
        ,'last_login_time' => 'datetime:Y-m-d\TH:i:sP'
    ];
    // protected $casts = ['first_login_time'
    //     , 'last_login_time'
    // ];
    // protected $dateFormat = 'Y-m-d\TH:i:sP';

    public function role(){
        return $this->belongsTo('App\Models\Role');
	}
	
    function submissions()
    {
        return $this->hasMany('App\Models\Submission');
    }

	function lops(){
		return $this->belongsToMany('App\Models\Lop');
    }
    
    function selected_assignment(){
        return $this->belongsTo('App\Models\Assignment', 'selected_assignment_id');
    }
}

//---------------------------------------------------------------------------------------------------------

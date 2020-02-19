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
    ];

    /**
	 * User ID to Username
	 *
	 * Converts user id to username (returns FALSE if user does not exist)
	 *
	 * @param $user_id
	 * @return bool
	 */
	public static function user_id_to_username($user_id)
	{
		if( ! is_numeric($user_id))
			return FALSE;
		$query = DB::table('users')->select('username')->where('id','=',$user_id)->get();;
		if ($query->all() == NULL)
			return FALSE;
        return $query->all()[0]->username;
	}

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
}

//---------------------------------------------------------------------------------------------------------

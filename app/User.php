<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

// use Illuminate\Database\Eloquent\Model;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'display_name', 'role_id'
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

    public static function delete_user($user_id)
	{
        $user_count = DB::table('users')->count();
		if ($user_id == 1){
			///Cannot delete the fist user (usually the one who installed the Judge)
			///First user will only be deleted from database
			return false;
        } 
        else if ($user_count < 2){
			///Cannot delete the only user there is.
			return false;
        }
		DB::beginTransaction();
        try {
            $username = User::user_id_to_username($user_id);
            if ($username === FALSE)
                return FALSE;
            DB::table('users')->delete($user_id);
            // DB::delete('submissions', array('username' => $username));
            // each time we delete a user, we should update all scoreboards
            // $this->load->model('scoreboard_model');
            // $this->scoreboard_model->update_scoreboards();
            // shell_exec("cd {$this->settings_model->get_setting('assignments_root')}; rm -r */*/{$username};");
            DB::commit();
			return TRUE; //success
        } catch (Exception $e) {
            DB::rollBack();
            
            throw new Exception($e->getMessage());
            return FALSE; //success
        }
	}
}

//---------------------------------------------------------------------------------------------------------

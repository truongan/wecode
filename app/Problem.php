<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $fillable = ['id','name','is_upload_only','diff_cmd','diff_arg'];

    public function get_id()
    {
        return $this->belongsToMany('App\Language');
    }
    
}

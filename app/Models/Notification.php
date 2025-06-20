<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $fillable = ['title', 'text', 'author', 'description', 'last_author'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'author');
    }
    
    public function last_user()
    {
        return $this->belongsTo('App\Models\User', 'last_author');
    }
}

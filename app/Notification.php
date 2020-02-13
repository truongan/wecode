<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $fillable = ['title', 'text', 'author', 'description', 'last_author'];
    public function user()
    {
        return $this->belongsTo('App\User', 'author');
    }
    public function last_user()
    {
        return $this->belongsTo('App\User', 'last_author');
    }
}

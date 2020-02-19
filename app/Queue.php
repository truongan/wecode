<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    //
    public function submission()
    {
        return $this->belongsTo('App\Submission', 'foreign_key', 'other_key');
    }
}

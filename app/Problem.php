<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;


class Problem extends Model
{
    protected $fillable = ['name','is_upload_only','diff_cmd','diff_arg'];
}

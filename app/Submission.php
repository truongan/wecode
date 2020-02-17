<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Submission extends Model
{
    protected $fillable = ['id', 'username','assignment_id','problem_id','is_final','time','status','pre_score'
                            ,'coefficient','file_name','language_id'];

    
}

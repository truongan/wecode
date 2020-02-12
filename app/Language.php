<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Language extends Model
{
    protected $fillable = ['name', 'extension','default_time_limit','default_memory_limit','sorting'];

    public static function order_languages()
    {
        $result = DB::table('languages')
                ->orderBy('sorting', 'asc')
                ->get();

        return ['languages' => collect($result)];
    }

}

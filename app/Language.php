<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function get_language($language){
		return collect([Language::where('name','=',$language)->first()]);
	}
}

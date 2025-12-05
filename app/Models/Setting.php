<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable = ["key", "value"];

    public static function get($key, $default = null)
    {
        $a = Setting::where("key", $key)->get();
        if ($a->count() > 0) {
            return $a->first()->value;
        } else {
            return $default;
        }
    }

    public static function set($key, $value)
    {
        $a = Setting::where("key", $key)->first();
        if ($a != null) {
            $a->value = $value;
            $a->save();
            return true;
        }
        return false;
    }

    public static function load_all()
    {
        $all = Setting::all()->reduce(function ($carry, $i) {
            $carry[$i->key] = $i->value;
            return $carry;
        }, []);
        return $all;
    }
    public static function find_by_key($key)
    {
        return Setting::where("key", $key)->first();
    }
}

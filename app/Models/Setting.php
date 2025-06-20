<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable = ['key', 'value'];
    
    static public function get($key){
        $a = Setting::where('key', $key)->get();
        if ($a->count() > 0)
            return $a->first()->value;
        else return NULL;
    }   

    static public function set($key, $value){
        $a = Setting::where('key', $key)->first();
        if ($a != NULL){
            $a->value = $value;
            $a->save();
            return true;
        }
        return false;
    }

    static public function load_all(){

        $all = Setting::all()->reduce(function($carry, $i){
            $carry[$i->key] = $i->value;
            return $carry;
        }, []);
        return $all;
    }
    static public function find_by_key($key){
        return Setting::where('key', $key)->first();
    }

}

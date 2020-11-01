<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Assignment;

class AssignmentOwnership extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{

            Schema::table('assignments', function (Blueprint $table) {
                $table->bigInteger('user_id')->nullable();
            });
        } catch(Exception $e){
            //do nothing; just ignore it.
        }
        foreach(Assignment::with('lops.users')->get() as $ass){
            if ($ass->lops->count() < 1) continue;
            $ass->user_id = $ass->lops->first()->users->filter(function($v,$k){return $v->role->name=='head_instructor';})->first()->id ?? None;
            $ass->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            //
            $table->dropColumn(['user_id']);

        });
    }
}

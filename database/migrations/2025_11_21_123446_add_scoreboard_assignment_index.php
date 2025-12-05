<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $group = DB::table('scoreboards')->select('id', 'assignment_id')->get()->groupBy('assignment_id');

        foreach ($group as $ass_id => $scb_s){
            for($i = 1; $i < $scb_s->count(); $i++){
                echo ("Duplicate scoreboard " . $scb_s[$i]->id . "for assignment $ass_id\n");
                DB::table('scoreboards')->where('id', $scb_s[$i]->id)->delete();
            }
        }

        Schema::table('scoreboards', function (Blueprint $table) {
            //
            $table->unique('assignment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scoreboards', function (Blueprint $table) {
            //
            $table->dropIndex(('scoreboards_assignment_id_unique'));
        });
    }
};

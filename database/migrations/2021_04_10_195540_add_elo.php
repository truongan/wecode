<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\User;
use App\Submission;
class AddElo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        try{

            Schema::table('users', function (Blueprint $table) {
                $table->Integer('elo')->nullable();
            });
            Schema::table('problems', function (Blueprint $table) {
                $table->Integer('elo')->nullable();
            });
        } catch(Exception $e){
            //do nothing; just ignore it.
        }
        DB::table('users')->update(['elo' => 10000]);
        DB::table('problems')->update(['elo' => 10000]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('problems', function (Blueprint $table) {
            $table->dropColumn(['elo']);
        });
    }
}

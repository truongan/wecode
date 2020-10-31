<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserProblemPermssion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('problems', function (Blueprint $table) {
            //
            $table->bigInteger('user_id')->nullable();
            $table->boolean('sharable')->nullable()->default(1);
        });
        DB::table('problems')->update(['sharable'=>1]); //All existing problems to be sharable
        DB::table('problems')->update(['user_id'=>1]); // All existing problem belong to the first user (admin)
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problems', function (Blueprint $table) {
            //
            $table->dropColumn(['user_id', 'sharable']);
        });
    }
}

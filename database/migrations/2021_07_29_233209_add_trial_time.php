<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrialTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //The number of seconds user can try the site out, after that, they will become guest on their next login
            //User with trial_time == 0  or null are permanent users and will not be demote to guest
            $table->unsignedInteger('trial_time')->nullable();
        });
        DB::table('roles')->insert(
            [
                ['name' =>  'guest', 'id' => 5 ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn(['trial_time']);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->date('freeze_time')->nullable($value = true);
        });
        Schema::table('scoreboards', function (Blueprint $table) {
            $table->boolean('is_after_freeze')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('freeze_time');
        });
        Schema::table('scoreboard', function (Blueprint $table) {
            $table->dropColumn('is_after_freeze');
        });
    }
};

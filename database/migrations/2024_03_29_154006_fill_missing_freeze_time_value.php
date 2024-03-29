<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('assignments')
            ->whereNull('freeze_time')
            ->update(['freeze_time' => DB::raw('CURRENT_TIMESTAMP')]);
        DB::table('assignments')
            ->whereNull('unfreeze_time')
            ->update(['unfreeze_time' => DB::raw('CURRENT_TIMESTAMP')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot be reversed
    }
};

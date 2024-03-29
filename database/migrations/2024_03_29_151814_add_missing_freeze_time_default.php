<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


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
            $table->timestamp('freeze_time')->useCurrent()->change();
            $table->timestamp('unfreeze_time')->useCurrent()->change();
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
            $table->timestamp('freeze_time')->nullable()->change();
            $table->timestamp('unfreeze_time')->nullable()->change();
        });
    }
};

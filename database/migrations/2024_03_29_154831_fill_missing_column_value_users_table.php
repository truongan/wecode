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
        DB::table('users')
            ->whereNull('image')
            ->update(['image' => 'images/logo_uit.png']);
        DB::table('users')
            ->whereNull('Name_school')
            ->update(['Name_school' => 'Đại học Công nghệ Thông tin - ĐHQG TP.HCM']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};

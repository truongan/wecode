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
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->default('images/logo_uit.png')->change();
            $table->string('Name_school')->default('Đại học Công nghệ Thông tin - ĐHQG TP.HCM')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
            $table->string('Name_school')->nullable()->change();
        });
    }
};

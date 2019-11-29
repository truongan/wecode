<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('username', 20);
            $table->char('password', 100);
            $table->char('display_name', 40);
            $table->char('email', 40);
            $table->char('role', 20);
            $table->char('passchange_key', 60);
            $table->dateTime('passchange_time');
            $table->dateTime('first_login_time');
            $table->dateTime('last_login_time');
            $table->unsignedSmallInteger('selected_assigment');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

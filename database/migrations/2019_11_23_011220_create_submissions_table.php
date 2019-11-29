<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('username',20);
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('problem_id');
            $table->tinyInteger('is_final');
            $table->dateTime('time');
            $table->char('status',100);
            $table->integer('pre_score');
            $table->char('coefficient',6);
            $table->char('file_name',30);
            $table->unsignedBigInteger('language_id');
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
        Schema::dropIfExists('submissions');
    }
}

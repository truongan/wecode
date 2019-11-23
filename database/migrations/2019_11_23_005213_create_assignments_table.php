<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('name', 150);
            $table->unsignedInteger('total_submits');
            $table->tinyInteger('open');
            $table->tinyInteger('score_board');
            $table->tinyInteger('javaexceptions');
            $table->dateTime('start_time');
            $table->dateTime('finish_time');
            $table->integer('extra_time');
            $table->text('late_rule');
            $table->text('participants');
            $table->char('moss_update', 30);
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
        Schema::dropIfExists('assignments');
    }
}

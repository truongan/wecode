<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagAndClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
        Schema::create('problem_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('problem_id');
            $table->bigInteger('tag_id');
            $table->timestamps();
            $table->index(['problem_id','tag_id']);
        });
        Schema::create('classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
        Schema::create('class_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('class_id');
            $table->bigInteger('user_id');
            $table->timestamps();
            $table->index(['class_id','user_id']);
        });
        Schema::create('assignment_class', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('assignment_id');
            $table->bigInteger('class_id');
            $table->timestamps();
            $table->index(['class_id','assignment_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('problem_tag');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('assignment_class');
        Schema::dropIfExists('class_user');
    }
}

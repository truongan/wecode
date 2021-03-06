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
            $table->text('text');
            $table->timestamps();
        });
        Schema::create('problem_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('problem_id');
            $table->bigInteger('tag_id');
            $table->timestamps();
            $table->index(['problem_id','tag_id']);
        });
        Schema::create('lops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->boolean('open');
            $table->timestamps();
        });
        Schema::create('lop_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('lop_id');
            $table->bigInteger('user_id');
            $table->timestamps();
            $table->index(['lop_id','user_id']);
        });
        Schema::create('assignment_lop', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('assignment_id');
            $table->bigInteger('lop_id');
            $table->timestamps();
            $table->index(['lop_id','assignment_id']);
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
        Schema::dropIfExists('lops');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('assignment_class');
        Schema::dropIfExists('class_user');
        Schema::dropIfExists('lop_user');
        Schema::dropIfExists('assignment_lop');

    }
}

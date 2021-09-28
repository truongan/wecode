<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_problem', function(Blueprint $table){
            $table->index('assignment_id');
            $table->index('problem_id');
            $table->bigIncrements('id');
        });

        //adding unique constraint may make coding more cumbersome in the future
        //decided to invest on duplicate removal command instead
        //AN NOTE :  2021-09-24
         
        // Schema::table('assignment_lop', function(Blueprint $table){
        //     $table->unique(['assignment_id', 'lop_id']);
        // });
        // Schema::table('assignment_problem', function(Blueprint $table){
        //     $table->unique(['assignment_id', 'problem_id']);
        // });
        // Schema::table('language_problem', function(Blueprint $table){
        //     $table->unique(['language_id', 'problem_id']);
        // });
        // Schema::table('lop_user', function(Blueprint $table){
        //     $table->unique(['lop_id', 'user_id']);
        // });
        // Schema::table('problem_tag', function(Blueprint $table){
        //     $table->unique(['problem_id', 'tag_id']);
        // });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('assignment_lop', function(Blueprint $table){
        //     $table->dropUnique('assignment_lop_assignment_id_lop_id_unique');
        // });
        // Schema::table('assignment_problem', function(Blueprint $table){
        //     $table->dropUnique('assignment_problem_assignment_id_problem_id_unique');
        // });
        // Schema::table('language_problem', function(Blueprint $table){
        //     $table->dropUnique('language_problem_language_id_problem_id_unique');
        // });
        // Schema::table('lop_user', function(Blueprint $table){
        //     $table->dropUnique('lop_user_lop_id_user_id_unique');
        // });
        // Schema::table('problem_tag', function(Blueprint $table){
        //     $table->dropUnique('problem_tag_problem_id_tag_id_unique');
        // });

    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorEditoralToProblem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{
            Schema::table('problems', function (Blueprint $table) {
                $table->string('author')->nullable();
                $table->string('editorial')->nullable();
            });
        } catch(Exception $e){
            //do nothing; just ignore it.
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problem', function (Blueprint $table) {
            $table->dropColumn(['author','editorial']);
        });
    }
}

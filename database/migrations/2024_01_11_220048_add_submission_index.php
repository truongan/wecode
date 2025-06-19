<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use App\Models\Exceptions;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        try {

            Schema::table('submissions', function ( $table) { 
    
                $table->dropIndex('submissions_problem_id_index');
                $table->dropIndex('submissions_user_id_index');
    
            });
        }
        catch(\Exception $e){
            echo "SHIT";
        }

        Schema::table('submissions', function ( $table) { 
            $table->index('problem_id'); 
            $table->index('user_id'); 
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('submissions', function ( $table) { 
            $table->dropIndex('submissions_problem_id_index');
            $table->dropIndex('submissions_user_id_index');
        } );
    }
};

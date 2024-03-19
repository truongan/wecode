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
        //
        if (!Schema::hasColumn('assignments', 'language_ids')){
            
            Schema::table('assignments', function (Blueprint $table) {
                $table->string('language_ids')->nullable();
            });
            
            DB::table('assignments')->update(['language_ids' => App\Language::all()->pluck('id')->implode(', ')]);
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (Schema::hasColumn('assignments', 'language_ids')){
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropColumn('language_ids');
            });
        }

    }
};

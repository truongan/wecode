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
        if (!Schema::hasColumn('problems', 'allow_input_download')){
            
            Schema::table('problems', function (Blueprint $table) {
                $table->boolean('allow_input_download')->nullable()->default(false);
            });
            
            DB::table('problems')->update(['allow_input_download' => false]);
        }
        if (!Schema::hasColumn('problems', 'allow_output_download')){
            
            Schema::table('problems', function (Blueprint $table) {
                $table->boolean('allow_output_download')->nullable()->default(false);
            });
            
            DB::table('problems')->update(['allow_output_download' => false]);
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
        if (Schema::hasColumn('problems]', 'allow_input_download')){
            Schema::table('problems]', function (Blueprint $table) {
                $table->dropColumn('allow_input_download');
            });
        }
        if (Schema::hasColumn('problems]', 'allow_output_download')){
            Schema::table('problems]', function (Blueprint $table) {
                $table->dropColumn('allow_output_download');
            });
        }
    }
};

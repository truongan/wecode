<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Scoreboard;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $list = Scoreboard::pluck('assignment_id', 'id');
        Schema::table('scoreboards', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scoreboards', function (Blueprint $table) {
            //
        });
    }
};

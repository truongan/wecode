<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('submissions', function (Blueprint $table) {
			//
			$table->string('coefficient')->change();
		});
		Setting::set('default_late_rule', 'delay < 0 ? 100 : 100*pow(0.99, delay/3600)');
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('submissions', function (Blueprint $table) {
			//
			$table->string('coefficient')->change();
		});
	}
};

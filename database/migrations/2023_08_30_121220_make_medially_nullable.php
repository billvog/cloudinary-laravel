<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::table('media', function (Blueprint $table) {
			$table->uuid('medially_id')->nullable()->change();
			$table->string('medially_type')->nullable()->change();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::table('media', function (Blueprint $table) {
			$table->uuid('medially_id')->nullable(false)->change();
			$table->string('medially_type')->nullable(false)->change();
		});
	}
};

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DataProviderLoaders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add the data provider to the loader
		Schema::table('input_packedload', function($table){
			$table->string('data_provider', 255);
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the column
		Schema::table('input_packedload', function($table){
			$table->dropColumn('data_provider');
		});
	}
}

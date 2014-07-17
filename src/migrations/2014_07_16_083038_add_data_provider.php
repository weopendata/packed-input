<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataProvider extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the data provider column
        Schema::table('input_packedartistmap', function($table){
            $table->string('data_provider', 255);
        });

        Schema::table('input_packedinstitutionmap', function($table){
            $table->string('data_provider', 255);
        });

        Schema::table('input_packedobjectmap', function($table){
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
        // Drop the data provider column
        Schema::table('input_packedartistmap', function($table){
            $table->dropColumn('data_provider');
        });

        Schema::table('input_packedinstitutionmap', function($table){
            $table->dropColumn('data_provider');
        });

        Schema::table('input_packedobjectmap', function($table){
            $table->dropColumn('data_provider');
        });
    }
}

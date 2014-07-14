<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PackedInit extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create an empty table for packed artists, objects and institutions mappers
        Schema::create('input_packedartistmap', function ($table) {

            $table->increments('id');
            $table->timestamps();

        });

        Schema::create('input_packedobjectmap', function ($table) {

            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('input_packedinstitutionmap', function ($table) {

            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('input_packedload', function ($table) {

            $table->increments('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the created tables

        Schema::drop('input_packedartistmap');
        Schema::drop('input_packedobjectmap');
        Schema::drop('input_packedinstitutionmap');
        Schema::drop('input_packedload');

    }
}

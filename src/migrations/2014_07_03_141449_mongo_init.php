<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MongoInit extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create collection in the MongoDB database
        Schema::connection('mongodb')->create('artists', function($collection)
        {

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the collection
        Schema::connection('mongodb')->drop('artists');
    }
}

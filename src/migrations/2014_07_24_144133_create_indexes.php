<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create indexes
        Schema::connection('mongodb')->table('institutions', function ($collection) {
            $collection->index('objectNumber');
            $collection->index('workPid');
            $collection->index('title');
            $collection->index('dateStartValue');
            $collection->index('dateEndValue');
            $collection->index('custodian');
            $collection->index('custodianIsilPid');
            $collection->index('custodianWikidataPid');
        });

        Schema::connection('mongodb')->table('objects', function ($collection) {
            $collection->index('objectName');
            $collection->index('objectNameAatId');
        });

        Schema::connection('mongodb')->table('artists', function ($collection) {
            $collection->index('creator');
            $collection->index('creatorViafPid');
            $collection->index('creatorRkdPid');
            $collection->index('creatorWikidataPid');
            $collection->index('creatorOdisPid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create indexes
        Schema::connection('mongodb')->table('institutions', function ($collection) {
            $collection->dropIndex('objectNumber');
            $collection->dropIndex('workPid');
            $collection->dropIndex('title');
            $collection->dropIndex('dateStartValue');
            $collection->dropIndex('dateEndValue');
            $collection->dropIndex('custodian');
            $collection->dropIndex('custodianIsilPid');
            $collection->dropIndex('custodianWikidataPid');
        });

        Schema::connection('mongodb')->table('objects', function ($collection) {
            $collection->dropIndex('objectName');
            $collection->dropIndex('objectNameAatId');
        });

        Schema::connection('mongodb')->table('artists', function ($collection) {
            $collection->dropIndex('creator');
            $collection->dropIndex('creatorViafPid');
            $collection->dropIndex('creatorRkdPid');
            $collection->dropIndex('creatorWikidataPid');
            $collection->dropIndex('creatorOdisPid');
        });
    }
}

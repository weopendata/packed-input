<?php

namespace Tdt\Input\EMLP\Load;

use MongoClient;

/**
 * Load data into a MongoDB datastore
 */
class Packedartist extends ALoader
{
    protected static $COLLECTION = 'artists';

    protected static $DB_NAME = 'packed';

    public function __construct($model, $command)
    {
        parent::__construct($model, $command);
    }

    public function init()
    {
        // Clear all existing artists from the respective data provider
        $data_provider = $this->loader->data_provider;

        $mongoConfig = \Config::get('database.connections.mongodb');

        $connString = 'mongodb://' . $mongoConfig['host'] . ':' . $mongoConfig['port'];

        $client = new MongoClient($connString);

        $artists = $client->selectCollection(self::$DB_NAME, self::$COLLECTION);

        $artists->remove(array('dataprovider' => $data_provider));
    }

    public function cleanUp()
    {

    }

    /**
     * Perform the load.
     *
     * @param array
     *
     * @return boolean|void
     */
    public function execute(&$chunk)
    {
        $this->log('------   Loading data  ------');

        // The mongo library allows hierarchical model assignment, but only
        // if the top level properties are declared as such.
        $artist = \Packed\Artist::create([]);

        // Every property is an array
        foreach ($chunk as $key => $value) {
            // Don't include the numeric keys, they are redundant
            if (!is_numeric($key)) {
                $artist->$key = $value;
            }

            dd($key);
        }

        try {
            $result = $artist->save();
        } catch (\Exception $ex) {

            $this->log('An error occured while saving the data into the NoSQL: ' . $ex->getMessage());

            $result = false;
        }

        if ($result) {
            $this->log('Successfully loaded the data into the NoSQL.');
        } else {
            $this->log('The data was not successfully stored into the NoSQL.', 'error');
        }

        $this->log('------   Done loading data  ------');

        return $result;
    }
}

<?php

namespace Tdt\Input\EMLP\Load;

/**
 * Load data into a MongoDB datastore
 */
class Packedartist extends ALoader
{
    public function __construct($model, $command)
    {
        parent::__construct($model, $command);
    }

    public function init()
    {
        // Clear all existing artists
        \Packed\Artist::truncate();
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
            $artist->$key = $value;
        }

        $result = $artist->save();

        if ($result) {
            $this->log('Successfully loaded the data into the NoSQL.');
        } else {
            $this->log('The data was not successfully stored into the NoSQL.', 'error');
        }

        $this->log('------   Done loading data  ------');

        return $result;
    }
}

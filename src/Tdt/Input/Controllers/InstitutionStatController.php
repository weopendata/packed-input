<?php

namespace Tdt\Input\Controllers;

/**
 * The statistics controller returns information about the
 * data provided by the certain instances and the processed enrichment
 * that has happened through various data sources
 *
 * @author Jan Vansteenlandt
 * @copyright aGPLv3
 */

use Packed\Artist;
use Packed\Institution;
use Packed\Object;
use MongoClient;

class InstitutionStatController extends \Controller
{

    protected static $COLLECTION = 'institutions';

    protected static $DB_NAME = 'packed';

    /**
     * Count the amount of workPid's regarding how many
     * of them have 1 to n descriptions
     *
     * @param int $n
     *
     * @return array
     */
    public function handle($n = 1)
    {
        $data = array();

        // Set up a connection to the mongodb, we cannot perform queries with
        // the mongoDB abstraction from the jenssegers repo
        // (e.g. distinct() function already provides incorrect information)

        $client = $this->getMongoClient();

        // Select the artist collection
        $institutions = $client->selectCollection(self::$DB_NAME, self::$COLLECTION);

        // Get the amount of 1 to n descriptions
        for ($i = 2; $i <= $n; $i++) {
            $data['1To' . $i . 'Descriptions'] = $this->getNDescriptions($institutions, $i);
        }

        // Get the amount of 1 to n representations
        for ($i = 2; $i <= $n; $i++) {
            $data['1To' . $i . 'Representations'] = $this->getNRepresentations($institutions, $i);
        }

        // Get the amount of works that have 1 to n reps and 1 to n descriptions
        $data['LOL'] = $this->getNRepAndDesc($institutions, 2);

        return $data;
    }

    /**
     * Count how many workPids relate to $n descriptions
     *
     * @param MongoCollection $institutions
     * @param int             $n
     *
     * @return int
     */
    private function getNDescriptions($institutions, $n)
    {
        // Add the dataPid references (unique) group on workPid
        $group = array(
                    '$group' => array(
                        '_id' => '$workPid',
                        'dataPid' => array(
                            '$addToSet' => '$dataPid'
                        )
                    )
                );

        // Only fetch the size of the references
        $project = array(
                    '$project' => array(
                        'size' => array('$size' => '$dataPid')
                    )
                );

        // Only pick out the $n sized ones
        $match = array(
                    '$match' => array(
                        'size' => $n
                    )
                );

        // Count the $n sized ones
        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $institutions->aggregate($group, $project, $match, $count);

        if (!empty($result['result'][0]['count'])) {
            return $result['result'][0]['count'];
        } else {
            return 0;
        }
    }

    /**
     * Count how many workPids relate to $n representations
     *
     * @param MongoCollection $institutions
     * @param int             $n
     *
     * @return int
     */
    private function getNRepresentations($institutions, $n)
    {
        // Add the representationPid references (unique) group on workPid
        $group = array(
                    '$group' => array(
                        '_id' => '$workPid',
                        'representationPid' => array(
                            '$addToSet' => '$representationPid'
                        )
                    )
                );

        // Only fetch the size of the references
        $project = array(
                    '$project' => array(
                        'size' => array('$size' => '$representationPid')
                    )
                );

        // Only pick out the $n sized ones
        $match = array(
                    '$match' => array(
                        'size' => $n
                    )
                );

        // Count the $n sized ones
        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $institutions->aggregate($group, $project, $match, $count);

        if (!empty($result['result'][0]['count'])) {
            return $result['result'][0]['count'];
        } else {
            return 0;
        }
    }

    /**
     * Count how many workPids relate to $n representations and $n descriptions
     *
     * @param MongoCollection $institutions
     * @param int             $n
     *
     * @return int
     */
    private function getNRepAndDesc($institutions, $n)
    {
        // Add the representationPid references (unique) group on workPid
        $unwind = array('$unwind' => '$workPid');

        $group = array(
                    '$group' => array(
                        '_id' => '$workPid',
                        'representationPid' => array(
                            '$addToSet' => '$representationPid'
                        ),
                        'dataPid' => array(
                            '$addToSet' => '$dataPid'
                        )
                    )
                );

        // Only fetch the size of the references
        $project = array(
                    '$project' => array(
                        'sizeRepresentation' => array('$size' => '$representationPid'),
                        'sizeDescription' => array('$size' => '$dataPid')
                    )
                );

        // Only pick out the $n sized ones
        $match = array(
                    '$match' => array(
                        'sizeRepresentation' => $n,
                        'sizeDescription' => $n
                    )
                );

        // Count the $n sized ones
        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        //dd($institutions->aggregate($unwind, $group, $project, $match, $count));

        $result = $institutions->aggregate($group, $project, $match, $count);

        if (!empty($result['result'][0]['count'])) {
            return $result['result'][0]['count'];
        } else {
            return 0;
        }
    }

    /**
     * Set up and return a mongo client
     *
     * @return MongoClient
     */
    protected function getMongoClient()
    {
        $mongoConfig = \Config::get('database.connections.mongodb');

        $connString = 'mongodb://' . $mongoConfig['host'] . ':' . $mongoConfig['port'];

        return new MongoClient($connString);
    }
}

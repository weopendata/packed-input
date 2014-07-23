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

class ObjectStatController extends StatController
{

    protected static $COLLECTION = 'objects';

    public function handle($dataProvider = null)
    {
        $data = array();

        // Fetch information about the objects

        // How many unique strings were delivered by the providers
        // Note: We don't take into account the links to the 3rd party data

        // Set up a connection to the mongodb, we cannot perform queries with
        // the mongoDB abstraction from the jenssegers repo
        // (e.g. distinct() function already provides incorrect information)

        $client = $this->getMongoClient();

        // Select the artist collection
        $objects = $client->selectCollection(self::$DB_NAME, self::$COLLECTION);

        // Get the amount of unique strings in the original data
        $data['uniqueStrings'] = $this->countUniqueStrings($objects);

        // Count the amount of unique AAT URI's
        $data['uniqueAatConcepts'] = $this->countUniqueAatUris($objects);

        // Count the amount of AAT URI matches
        $data['matchURI'] = $this->countUriMatch($objects);

        // Count the amount of en additions
        $data['enStrings'] = $this->countLingualAdditions($objects, 'en');

        // Count the amount of fr additions
        $data['frStrings'] = $this->countLingualAdditions($objects, 'fr');

        // Count the amount of de additions
        $data['deStrings'] = $this->countLingualAdditions($objects, 'de');

        // Count the amount of nl additions
        $data['nlStrings'] = $this->countLingualAdditions($objects, 'nl');

        return \Response::json($data);
    }

    /**
     * Count the amount of unique strings
     *
     * @param MongoCollection $objects
     *
     * @return int
     */
    private function countUniqueStrings($objects)
    {
        $unwind = array('$unwind' => '$objectName');

        $group = array(
                    '$group' => array(
                        '_id' => '$objectName',
                        'unique' => array(
                            '$addToSet' => '$objectName'
                        )
                    )
                );

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array('$sum' => 1)
                    )
                );

        $result = $objects->aggregate($unwind, $group, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of unique AAT URI's
     *
     * @param MongoCollection $objects
     *
     * @return int
     */
    private function countUniqueAatUris($objects)
    {
        $unwind = array('$unwind' => '$objectNameAatPid');

        $group = array(
                    '$group' => array(
                        '_id' => '$objectNameAatPid',
                        'unique' => array(
                            '$addToSet' => '$objectNameAatPid'
                        )
                    )
                );

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $objects->aggregate($unwind, $group, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of creators that match with an amount of provided URI's
     *
     * @param MongoCollection $objects
     * @param int             $amount
     *
     * @return int
     */
    private function countUriMatch($objects)
    {
        $match = array(
                    '$match' => array(
                        'objectNameAatPid' => array(
                            '$not' => array(
                                '$size' => 0
                            )
                        )
                    )
                );

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $objects->aggregate($match, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the multilingual additions overall (no unique)
     *
     * @param MongoCollection $objects
     * @param string          $lang
     *
     * @return int
     */
    private function countLingualAdditions($objects, $lang)
    {
        // Count the preferredName additions
        $unwindKey = '$AAT.preferredNames.' . $lang;

        $unwind = array(
                    '$unwind' => $unwindKey
                );

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array('$sum' => 1)
                    )
                );

        $result = $objects->aggregate($unwind, $count);

        $preferredCount = $result['result'][0]['count'];

        // Count the nonPreferredNames
        $unwindKey = '$AAT.nonPreferredNames.' . $lang;

        $unwind = array(
                    '$unwind' => $unwindKey
                );

        $result = $objects->aggregate($unwind, $count);

        $nonPreferredCount = $result['result'][0]['count'];

        return $preferredCount + $nonPreferredCount;
    }
}

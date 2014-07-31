<?php

namespace Tdt\Input\Controllers;

use MongoClient;
use Packedinstitution;
use Response;

/**
 * Controller that helps building the discovery document.
 *
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class QueryController extends \Controller
{

    protected static $DB_NAME = 'packed';

    protected static $SUGGEST_PAGE_SIZE = 30;

    protected static $QUERY_PAGE_SIZE = 60;

    /**
     * Handle the query
     */
    public function handle()
    {
        $input = \Input::all();

        // If nothing is given, return a 400
        if (empty($input)) {
            \App::abort(400, "Please provide a parameter with your request (objectNumber, objectName, creator, title).");
        }

        $results = array();

        // Check for creator related parameters
        $artistResults = array();

        $creator = \Input::get('creator');

        // Check if index is true or false
        $index = \Input::get('index', false);

        $index = (bool) $index;

        if (!empty($creator)) {

            $artists = $this->getCollection('artists');

            $works = $this->getCollection('institutions');

            $filter = array(
                        'creator' => array(
                            '$regex' => $creator,
                            '$options' => 'i'
                        )
                    );

            // Define which properties we don't want
            $properties = array(
                            '_id' => 0,
                            'created_at' => 0,
                            'updated_at' => 0,
                        );

            if ($index) {

                $filter = array(
                            '$or' => array(
                                array(
                                    'uniqueNameVariants' => array(
                                        '$regex' => '.*' . $creator . '.*',
                                        '$options' => 'i'
                                    ),
                                ), array(
                                    'creator' => array(
                                        '$regex' => '.*' . $creator . '.*',
                                        '$options' => 'i'
                                    )

                                )
                            )
                        );
            }

            $artistCursor = $artists->find($filter, $properties);

            foreach ($artistCursor as $artist) {

                if (!empty($artist['creatorId'])) {

                    // Foreach artist, search for accompanying works
                    $filter = array('creatorId' => $artist['creatorId']);

                    // TODO: add the object filters !!

                    $properties = array(
                                    '_id' => 0,
                                    'dateIso8601Range' => 0,
                                    'updated_at' => 0,
                                    'created_at' => 0,
                                );

                    $worksCursor = $works->find($filter, $properties);

                    $artist['works'] = array();

                    foreach ($worksCursor as $work) {
                        array_push($artist['works'], $work);
                    }
                }

                array_push($artistResults, $artist);
            }

            $results['artists'] = $artistResults;

        } else {

            $workResults = array();

            $parameters = array('objectNumber', 'title', 'objectName');

            $filterParameters = array();

            foreach ($parameters as $parameter) {

                $val = \Input::get($parameter);

                if (!empty($val)) {
                    $filterParameters[$parameter] = \Input::get($parameter);
                }
            }

            // Build the $and clause

            $and = array();

            foreach ($filterParameters as $filterParam => $filterVal) {

                $clause = array(
                            $filterParam => array(
                                '$regex' => '.*' . $filterVal . '.*',
                                '$options' => 'i'
                            )
                        );

                array_push($and, $clause);
            }

            // Get the objects with the build up filter
            $works = $this->getCollection('institutions');
            $objects = $this->getCollection('objects');
            $artists = $this->getCollection('artists');

            // Properties that don't need to be returned
            $properties = array(
                            '_id' => 0,
                            'created_at' => 0,
                            'updated_at' => 0,
                        );

            $filter = array('$and' => $filter);

            $worksCursor = $works->find($filter, $properties);

            foreach ($worksCursor as $work) {

                if (!empty($work['objectNameId'])) {

                    $filter = array('objectNameId' => $work['objectNameId']);

                    $objectCursor = $objects->find($filter, $properties);

                    $work['objects'] = array();

                    foreach ($objectCursor as $object) {
                        array_push($work['objects'], $object);
                    }
                }

                if (!empty($work['creatorId'])) {

                    $filter = array('creatorId' => $work['creatorId']);

                    $artistCursor = $artists->find($filter, $properties);

                    $work['artists'] = array();

                    foreach ($artistCursor as $artist) {
                        array_push($work['artists'], $artist);
                    }
                }

                array_push($workResults, $work);
            }

            $results['works'] = $workResults;
        }

        // Check for object related parameters

        return \Response::json($results);
    }

    /**
     * Fetch suggestions based on the query string parameter from the institutions (=works) collection
     *
     * Options are: creator, objectName, title and objectNumber
     * TODO: more suggestions when index is on!
     *
     * @return Response
     */
    public function suggest()
    {
        $parameters = array('creator', 'objectName', 'title', 'objectNumber');

        $searchVal = '';
        $searchKey = '';

        // Scan the query string parameters for a first hit
        foreach (\Input::get() as $key => $val) {
            if (in_array($key, $parameters)) {
                $searchKey = $key;
                $searchVal = $val;
            }
        }

        if (empty($searchKey)) {
            return Response::json(array());
        }

        // Get the mongo client
        $client = $this->getMongoClient();

        // Get the collection
        $works = $client->selectCollection(self::$DB_NAME, 'institutions');

        // Fetch the results, only with the necessary field
        $query = array(
                    $searchKey => array(
                            '$regex' => '.*' . $searchVal . '.*',
                            '$options' => 'i'
                    ),
                );

        // Make the query and only retrieve the field that matches the search key
        $cursor = $works->find($query, array($searchKey => 1))->limit(self::$SUGGEST_PAGE_SIZE);

        if (!$cursor->hasNext()) {
            return Response::json(array());
        }

        $results = array();

        foreach ($cursor as $result) {
            if (is_array($result[$searchKey])) {

                foreach ($result[$searchKey] as $val) {
                    if (!in_array($val, $results)) {
                        array_push($results, $val);
                    }
                }
            } else {
                if (!in_array($result[$searchKey], $results)) {
                    array_push($results, $result[$searchKey]);
                }
            }

        }

        return Response::json($results);
    }

    /**
     * Return a collection through the mongoclient
     *
     * @param string $collection
     *
     * @return MongoCollection
     */
    private function getCollection($collection)
    {

        $client = $this->getMongoClient();

        $mongoCollection = $client->selectCollection(self::$DB_NAME, $collection);

        return $mongoCollection;
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

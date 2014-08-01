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

    protected static $SUGGEST_PAGE_SIZE = 1000;

    protected static $QUERY_PAGE_SIZE = 100;

    /**
     * Handle the query
     *
     * @return Response
     */
    public function handle()
    {
        $input = \Input::all();

        // Fetch limit and offset
        $limit = \Input::get('limit', self::$QUERY_PAGE_SIZE);

        $offset = \Input::get('offset', 0);

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

        // Check if dates have to search in a normalized way or not
        $normalized = \Input::get('normalized', false);

        $normalized = (bool) $normalized;

        // If a creator has been passed, search for matching
        // creatorIds in the artist collection, this collection
        // contains name variants and offers better search results (if index is set to true)
        if (!empty($creator)) {

            // Start from artists, and find related works through the
            // institutions collection

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

            $artistCursor = $artists->find($filter, $properties)->skip($offset)->limit($limit);

            foreach ($artistCursor as $artist) {

                if (!empty($artist['creatorId'])) {

                    // Foreach artist, search for accompanying works
                    $creatorFilter = array('creatorId' => $artist['creatorId']);

                    $worksFilter = $this->buildWorksFilter();

                    $filter = array($creatorFilter);

                    foreach ($worksFilter as $workFilter) {
                        array_push($filter, $workFilter);
                    }

                    $filter = array('$and' => $filter);

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

                    if (!empty($artist['works']) && !empty($worksFilter)) {
                        array_push($artistResults, $artist);
                    } else if (empty($worksFilter)) {
                        array_push($artistResults, $artist);
                    }
                }
            }

            $results['artists'] = $artistResults;

        } else {

            // Start from institutions collections (= works) and find
            // related artists and objects
            $workResults = array();

            $filter = $this->buildWorksFilter();

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

            // Find the works matching the filter
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

        return \Response::json($results);
    }

    /**
     * Create a works filter based on the relevant query string parameters
     *
     * @return array
     */
    private function buildWorksFilter()
    {
        $parameters = array('objectDetail', 'objectName', 'startDate', 'endDate');

        // Check if dates have to search in a normalized way or not
        $normalized = \Input::get('normalized', false);

        $normalized = (bool) $normalized;

        $filterParameters = array();

        foreach ($parameters as $parameter) {

            $val = \Input::get($parameter);

            if (!empty($val)) {
                $filterParameters[$parameter] = \Input::get($parameter);
            }
        }

        // Build the $and clause
        $and = array();

        // Check for objectDetail (objectNumber or title)
        if (!empty($filterParameters['objectDetail'])) {

            $clause = array(
                '$or' => array(
                        array(
                            'objectNumber' => array(
                                '$regex' => '.*' . $filterParameters['objectDetail'] . '.*',
                                '$options' => 'i'
                            )
                        ), array(
                            'title' => array(
                                '$regex' => '.*' . $filterParameters['objectDetail'] . '.*',
                                '$options' => 'i'
                            )
                        )
                    )
                );

            array_push($and, $clause);
        }

        // Check for objectName
        if (!empty($filterParameters['objectName'])) {

            $clause = array(
                'objectName' => array(
                    '$regex' => '.*' . $filterParameters['objectName'] . '.*',
                    '$options' => 'i'
                    )
                );

            array_push($and, $clause);
        }

        // Check for date parameters
        if (!empty($filterParameters['startDate']) || !empty($filterParameters['endDate'])) {

            $startDate = @$filterParameters['startDate'];
            $endDate = @$filterParameters['endDate'];

            if (empty($startDate)) {
                // Arbitrary lower boundry

                $startDate = -5000;
            }

            if (empty($endDate)) {
                // Arbitrary upper boundry

                $endDate = 3000;
            }

            if ($normalized) {

                $clause = array(
                            'dateIso8601Range' => array(
                                '$gte' => $startDate,
                                '$lte' => $endDate,
                            )
                        );

                array_push($and, $clause);
            } else {

                $clause = array(
                            '$or' => array(
                                array('dateStartValue' => $startDate),
                                array('dateStartValue' => $endDate),
                                array('dateEndValue' => $startDate),
                                array('dateEndValue' => $endDate)
                            )
                        );

                array_push($and, $clause);
            }
        }

        return $and;
    }

    /**
     * Fetch suggestions based on the query string parameter from the institutions (=works) collection
     *
     * Options are: creator, objectName, title and objectNumber
     *
     * @return Response
     */
    public function suggest()
    {
        $parameters = array('creator', 'objectName', 'objectDetail');

        $searchVal = '';
        $searchKey = '';

        // Check if index is active
        $index = \Input::get('index', false);

        $index = (bool) $index;

        // Scan the query string parameters for a first hit
        foreach (\Input::get() as $key => $val) {
            if (in_array($key, $parameters)) {
                $searchKey = $key;
                $searchVal = $val;

                break;
            }
        }

        if (empty($searchKey)) {
            return Response::json(array());
        }

        // Get the mongo client
        $client = $this->getMongoClient();

        // Prepare the end result array
        $results = array();

        if ($searchKey == 'creator') {

            // Get the artists collection
            $artists = $client->selectCollection(self::$DB_NAME, 'artists');

            // Fetch the results, only with the necessary field
            // If it's a creator and the index is active, also search in the name variant_set(variant, value)
            $query = array(
                $searchKey => array(
                    '$regex' => '.*' . $searchVal . '.*',
                    '$options' => 'i',
                ),
            );

            if ($index) {

                $nameVariants = array(
                    'uniqueNameVariants' => array(
                        '$regex' => '.*' . $searchVal . '.*',
                        '$options' => 'i'
                        )
                    );

                $query = array('$or' => array($nameVariants, $query));
            }

            // Make the query and only retrieve the field that matches the search key
            $cursor = $artists->find($query);

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

        } else {

            // Get the works collection
            $works = $client->selectCollection(self::$DB_NAME, 'institutions');

            // Fetch the results, only with the necessary field
            // If it's a creator and the index is active, also search in the name variant_set(variant, value)

            // Make a distinction between objectName and objectDetail (=> title or objectNumber)
            $query = array();

            if ($searchKey == 'objectName') {

                $query = array(
                    'objectName' => array(
                        '$regex' => '.*' . $searchVal . '.*',
                        '$options' => 'i'
                        ),
                    );

                // Make the query and only retrieve the field that matches the search key
                $cursor = $works->find($query)->limit(self::$SUGGEST_PAGE_SIZE);

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

            } else {

                $query = array(
                            '$or' => array(
                                array(
                                    'objectNumber' => array(
                                        '$regex' => '.*' . $searchVal . '.*',
                                        '$options' => 'i'
                                    )
                                ),
                                array(
                                    'title' => array(
                                        '$regex' => '.*' . $searchVal . '.*',
                                        '$options' => 'i'
                                    )
                                )
                            )
                        );

                // Make the query and only retrieve the field that matches the search key
                $cursor = $works->find($query)->limit(self::$SUGGEST_PAGE_SIZE);

                if (!$cursor->hasNext()) {
                    return Response::json(array());
                }

                $results = array();

                foreach ($cursor as $result) {
                    if (is_array($result['title'])) {

                        foreach ($result['title'] as $val) {
                            if (!in_array($val, $results)) {
                                array_push($results, $val);
                            }
                        }
                    } else {
                        if (!in_array($result['title'], $results)) {
                            array_push($results, $result['title']);
                        }
                    }

                    if (is_array($result['objectNumber'])) {

                        foreach ($result['objectNumber'] as $val) {
                            if (!in_array($val, $results)) {
                                array_push($results, $val);
                            }
                        }
                    } else {
                        if (!in_array($result['objectNumber'], $results)) {
                            array_push($results, $result['objectNumber']);
                        }
                    }
                }

                return Response::json($results);
            }
        }
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

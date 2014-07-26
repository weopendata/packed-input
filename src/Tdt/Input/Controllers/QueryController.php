<?php

namespace Tdt\Input\Controllers;

use MongoClient;
use Packedinstitution;
use Response;

/**
 * Controller that helps building the discovery document.
 */
class QueryController extends \Controller
{

    protected static $DB_NAME = 'packed';

    protected static $SUGGEST_PAGE_SIZE = 15;

    protected static $QUERY_PAGE_SIZE = 60;

    /**
     * Handle the query
     */
    public function handle()
    {
        $parameters = array('creator', 'objectName', 'title', 'objectNumber');

        $filters = array();

        // Scan the query string parameters for a first hit
        foreach (\Input::get() as $key => $val) {
            if (in_array($key, $parameters)) {
                $filters[$key] = $val;
            }
        }

        $mongoFilter = array();

        foreach ($filters as $filter) {
            array_push($mongoFilter, $filter);
        }

        // Get the mongo client
        $client = $this->getMongoClient();

        // Get the collection
        $works = $client->selectCollection(self::$DB_NAME, 'institutions');

        // Get the paging info
        $limit = \Input::get('limit', self::$QUERY_PAGE_SIZE);

        $offset = \Input::get('offset', 0);

        // Make the query and create the resulting array
        $fields = array(
                    'created_at' => 0,
                    'updated_at' => 0,
                    '_id' => 0,
                    'databaseNumber' => 0
                );

        $cursor = $works->find($filters, $fields)->skip($offset)->limit($limit);

        $results = array();

        foreach ($cursor as $result) {
            array_push($results, $result);
        }

        return \Response::json($results);
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

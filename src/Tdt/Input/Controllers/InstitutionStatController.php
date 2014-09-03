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
use Tdt\Core\Datasets\Data;
use Tdt\Core\Formatters\CSVFormatter;

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
    public function handle($n = 4)
    {
        $data = new \stdClass();

        // Set up a connection to the mongodb, we cannot perform queries with
        // the mongoDB abstraction from the jenssegers repo
        // (e.g. distinct() function already provides incorrect information)

        $client = $this->getMongoClient();

        // Select the artist collection
        $institutions = $client->selectCollection(self::$DB_NAME, self::$COLLECTION);

        // Get the amount of 1 to n descriptions
        for ($i = 2; $i <= $n; $i++) {

            $description = '1To' . $i . 'Description';

            $data->$description = $this->getNDescriptions($institutions, $i);
        }

        // Get the amount of 1 to n representations
        for ($i = 2; $i <= $n; $i++) {

            $representation = '1To' . $i . 'Representation';

            $data->$representation = $this->getNRepresentations($institutions, $i);
        }

        // Get the amount of works that have 1 to n reps and 1 to n descriptions
        for ($i = 2; $i <= $n; $i++) {

            $repAndDesc = '1To' . $i . 'RepresentationAndDescription';

            $data->$repAndDesc = $this->getNRepAndDesc($institutions, $i);
        }

        $dataObject = new Data();
        $dataObject->data = array($data);
        $dataObject->is_semantic = false;

        $formatter = new CSVFormatter();

        return $formatter->createResponse($dataObject);
    }

    /**
     * Return the amount of normalized works and non normalized works
     *
     * @return Response
     */
    public function normalized()
    {
        $dataResult = array();

        // Set up a connection to the mongodb, we cannot perform queries with
        // the mongoDB abstraction from the jenssegers repo
        // (e.g. distinct() function already provides incorrect information)

        $client = $this->getMongoClient();

        // Select the artist collection
        $institutions = $client->selectCollection(self::$DB_NAME, self::$COLLECTION);

        // Count how many works there are per year
        // with only dateStartValue and dateEndValue
        // and then with dateIso8601Range
        $normalizedResults = $this->getNormWorksPerYear($institutions);

        $nonNormalizedResults = $this->getNonNormWorksPerYear($institutions);

        $normalizedKeys = array_keys($normalizedResults);

        $nonNormalizedKeys = array_keys($nonNormalizedResults);

        $count = max(count($normalizedKeys), count($nonNormalizedKeys));

        $normHeader = 'amount of works found through normalization';

        $nonNormHeader = 'amount of works found without normalization';

        // Add the normalized and non normalized keys
        for ($i = 0; $i < $count; $i++) {

            $data = new \stdClass();

            if (!empty($normalizedKeys[$i]) || is_numeric(@$normalizedKeys[$i])) {

                $key = $normalizedKeys[$i];

                $data->normalizedYear = $key;

                $data->$normHeader = $normalizedResults[$key];
            } else {
                $data->normalizedYear = '';
                $data->normHeader = '';
            }

            if (!empty($nonNormalizedKeys[$i]) || is_numeric(@$nonNormalizedKeys[$i])) {

                $key = $nonNormalizedKeys[$i];

                $data->nonNormalizedYear = $key;

                $data->$nonNormHeader = $nonNormalizedResults[$key];
            } else {
                $data->nonNormalizedYear = '';
                $data->nonNormHeader = '';
            }

            array_push($dataResult, $data);
        }

        $dataObject = new Data();
        $dataObject->data = $dataResult;
        $dataObject->is_semantic = false;

        $formatter = new CSVFormatter();

        return $formatter->createResponse($dataObject);
    }

    /**
     * Get the frequency on the amount of works per year
     * based on non normative dates
     *
     * @param MongoCollection $institutions
     *
     * @return array
     */
    private function getNonNormWorksPerYear($institutions)
    {
        $data = array();

        // Expand records per date
        $unwind = array('$unwind' => '$dateStartValue');

        // Group all of the works per year
        $group = array(
                    '$group' => array(
                        '_id' => '$dateStartValue',
                        'works' => array('$addToSet' => '$workPid')
                    )
                );

        // Calculate the size of the works
        $count = array(
                    '$project' => array(
                        '_id' => 0,
                        'date' => '$_id',
                        'amount' => array(
                            '$size' => '$works'
                        )
                    )
                );

        $sort = array(
                    '$sort' => array('date' => 1)
                );

        $resultStartDate = $institutions->aggregate($unwind, $group, $count, $sort);

         // Expand records per date
        $unwind = array('$unwind' => '$dateEndValue');

        // Group all of the works per year
        $group = array(
                    '$group' => array(
                        '_id' => '$dateEndValue',
                        'works' => array('$addToSet' => '$workPid')
                    )
                );

        // Calculate the size of the works
        $count = array(
                    '$project' => array(
                        '_id' => 0,
                        'date' => '$_id',
                        'amount' => array(
                            '$size' => '$works'
                        )
                    )
                );

        $sort = array(
                    '$sort' => array('date' => 1)
                );

        $resultEndDate = $institutions->aggregate($unwind, $group, $count, $sort);


        // Normally there should be a result, but just to be sure check beforehand
        if (!empty($resultStartDate['result'])) {
            // Build an assoc array where the amount of work
            foreach ($resultStartDate['result'] as $date) {
                $data[$date['date']] = $date['amount'];
            }
        } else {
            \Log::info("No results were found after calculating the works per non normalized year (startDate).");
        }

        if (!empty($resultEndDate['result'])) {
            // Add the amount of works
            foreach ($resultEndDate['result'] as $date) {
                if (!array_key_exists($date['date'], $data)) {
                    $data[$date['date']] = $date['amount'];
                } else {
                    $data[$date['date']] += $date['amount'];
                }
            }
        }

        return $data;
    }

    /**
     * Get the frequency on the amount of works per year
     * based on non normative dates
     *
     * @param MongoCollection $institutions
     *
     * @return array
     */
    private function getNormWorksPerYear($institutions)
    {
        $data = array();

        // Expand records per date
        $unwind = array('$unwind' => '$dateIso8601Range');

        // Group all of the works per year
        $group = array(
                    '$group' => array(
                        '_id' => '$dateIso8601Range',
                        'works' => array(
                            '$addToSet' => '$workPid'
                        )
                    )
                );

        // Calculate the size of the works
        $project = array(
                    '$project' => array(
                        //'_id' => 0,
                        'date' => '$_id',
                        'amount' => array(
                            '$size' => '$works'
                        )
                    )
                );

        $sort = array(
                    '$sort' => array('date' => 1)
                );

        $result = $institutions->aggregate($unwind, $group, $project, $sort);

        // Build an assoc array where the amount of work
        foreach ($result['result'] as $date) {
            $data[$date['date']] = $date['amount'];
        }

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
        $unwind = array('$unwind' => '$workPid');

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

        $result = $institutions->aggregate($unwind, $group, $project, $match, $count);

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
        $unwind = array('$unwind' => '$workPid');

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

        $result = $institutions->aggregate($unwind, $group, $project, $match, $count);

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
        $unwind = array('$unwind' => '$workPid');

        // Add the id references (unique) group on workPid
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

        $result = $institutions->aggregate($unwind, $group, $project, $match, $count);

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

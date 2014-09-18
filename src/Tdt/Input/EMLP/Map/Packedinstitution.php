<?php

namespace Tdt\Input\EMLP\Map;

use Goutte\Client;
use GuzzleHttp\Exception\RequestException;

class Packedinstitution extends AMapper
{
    public function __construct($model, $command)
    {
        parent::__construct($model, $command);
    }

    public function init()
    {

    }

    /**
     * Execute the mapping of a chunk of data
     * Every piece in the array will contain data that enriches
     * the original data (artist, art object) and will be identified
     * separately in the returning array for easy processing/querying later on
     *
     * @return array
     */
    public function execute(&$chunk)
    {
        // Choose the first value of the chunk as an identifier to perform some logging
        $id = reset($chunk);

        // Certain values of the chunk need to be split on the base of a semi-colon
        $multiValues = array(
                        'workPid',
                        'title',
                        'creator',
                        'creatorId',
                        'objectName',
                        'objectNameId',
                        'dateStartPrecision',
                        'dateStartValue',
                        'dateEndPrecision',
                        'dateEndValue',
                        'dateIso8601'
                    );

        foreach ($multiValues as $key) {

            if (array_key_exists($key, $chunk)) {
                if (!empty($chunk[$key])) {

                    $chunk[$key] = explode(';', $chunk[$key]);
                } else {

                    $chunk[$key] = array();
                }
            }

            // Prefix creatorId
            if ($key == 'creatorId') {

                foreach ($chunk[$key] as $index => $creatorId) {
                    $chunk[$key][$index] = strtolower($this->mapper->data_provider) . '_' . $creatorId;
                }
            }
        }

        // Add the original data provider
        $chunk['dataprovider'] = $this->mapper->data_provider;

        $this->log('------   Mapping data  ------');

        $timeout = 1;

        $this->log("Enriching data for chunk identified by $id, waiting $timeout seconds before starting HTTP requests.");

        sleep($timeout);

        $chunk = $this->enrichWithWiki($chunk);

        // Explode the date range and fill in the gap
        if (!empty($chunk['dateIso8601'])) {

            // Sometimes the value is a single value (small remark this date notation is nowhere near ISO 8601?)
            $range = explode('/', $chunk['dateIso8601'][0]);

            if (count($range) == 2 && $range[0] < $range[1] && is_numeric($range[0]) && is_numeric($range[1])) {

                $dateRange = array();

                for ($i = $range[0]; $i <= $range[1]; $i++) {
                    array_push($dateRange, (int) $i);
                }

                $chunk['dateIso8601Range'] = $dateRange;
            } else {
                $chunk['dateIso8601Range'] = array(@$range[0]);
            }

        }

        $this->log('----- Done mapping data -----');

        return $chunk;
    }

    /**
     * Enrich the data in the chunk with data from WikiData data source
     *
     * @param array $chunk
     *
     * @return array
     */
    private function enrichWithWiki($chunk)
    {
        if (!empty($chunk['custodianWikidataPid'])) {

            $chunk['Wikidata'] = array('website' => '', 'geo' => '');

            $wikiUri = $chunk['custodianWikidataPid'];

            $client = new Client();

            try {

                // Prepare the crawler
                $crawler = $client->request('GET', $wikiUri);

            } catch (RequestException $ex) {

                $this->log('An error has occurred while retrieving wiki data:' . $ex->getMessage(), 'error');

                return $chunk;
            }

            // Fetch the geo coordinates
            $geoCoordinates = $crawler->filter('div[id="P625"]');

            try {

                preg_match('/\s*(\d{1}.*)\s*\[edit\].*/', $geoCoordinates->text(), $matches);

                $geoCoordinates = @$matches[1];

                if (!empty($geoCoordinates)) {
                    $this->log('Geo coordinates found from the wikidata page.');

                    $chunk['Wikidata']['geo'] = $geoCoordinates;
                }

            } catch (\InvalidArgumentException $ex) {
                $this->log('No coordinates found from the wikidata page.');
            }

            // Fetch the official website
            $website = $crawler->filter('div[id="P856"]');

            try {

                preg_match('/\s*(http:\/\/.+)\s*.*/', $website->text(), $matches);

                $website = @$matches[1];

                if (!empty($website)) {
                    $this->log('Website found from the wikidata page.');

                    $chunk['Wikidata']['website'] = $website;
                }

            } catch (\InvalidArgumentException $ex) {
                $this->log('No coordinates found from the wikidata page.');
            }

        } else {

            $chunk['Wikidata'] = array();

            $this->log("No Wikidata link was found, returning data without Wikidata enrichment.");
        }

        return $chunk;
    }
}

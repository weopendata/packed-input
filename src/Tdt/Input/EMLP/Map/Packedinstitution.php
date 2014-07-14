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
     * return array
     */
    public function execute(&$chunk)
    {
        // Choose the first value of the chunk as an identifier to perform some logging
        $id = reset($chunk);

        $this->log('------   Mapping data  ------');

        $timeout = 5;

        $this->log("Enriching data for chunk identified by $id, waiting $timeout seconds before starting HTTP requests.");

        sleep($timeout);

        $chunk = $this->enrichWithWiki($chunk);

        $this->log('----- Done mapping data -----');

        return $chunk;
    }

    /**
     * Enrich the data in the chunk with data from WikiData data source
     *
     * TODO
     *
     * @param array $chunk
     *
     * @return array
     */
    private function enrichWithWiki($chunk)
    {
        if (!empty($chunk['custodianWikidataPid'])) {

            $chunk['Wikidata'] = array();

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

            preg_match('/\s*(\d{1}.*)\s*\[edit\].*/', $geoCoordinates->text(), $matches);

            $geoCoordinates = @$matches[1];

            if (!empty($geoCoordinates)) {
                $chunk['Wikidata']['geo'] = $geoCoordinates;
            }

            // Fetch the official website
            $website = $crawler->filter('div[id="P856"]');

            preg_match('/\s*(http:\/\/.+)\s*.*/', $website->text(), $matches);

            $website = @$matches[1];

            if (!empty($website)) {
                $chunk['Wikidata']['website'] = $website;
            }

        } else {

            $chunk['Wikidata'] = array();

            $this->log("No Wikidata link was found, returning data without Wikidata enrichment.");
        }

        return $chunk;
    }
}

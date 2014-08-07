<?php

namespace Tdt\Input\EMLP\Map;

use Goutte\Client;
use GuzzleHttp\Exception\RequestException;

class Packedartist extends AMapper
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
     * @param array $chunk
     *
     * @return array
     */
    public function execute(&$chunk)
    {
        // Choose the first value of the chunk as an identifier to perform some logging
        $id = reset($chunk);

        // All values are possible multi values
        foreach ($chunk as $key => $value) {

            if (!empty($value)) {
                $chunk[$key] = explode(';', $value);
            } else {
                $chunk[$key] = array();
            }
        }

        // Map the creator value to the 'creator' key

        if (!empty($chunk[$this->mapper->creator_column])) {

            $chunk['creator'] = $chunk[$this->mapper->creator_column];

            if ($this->mapper->creator_column != 'creator') {
                unset($chunk[$this->mapper->creator_column]);
            }

        } else {
            $this->log('The provided creator column could not be found or is empty.', 'error');
        }

        // Add the original data provider
        $chunk['dataprovider'] = $this->mapper->data_provider;

        $this->log('------   Mapping data  ------');

        $timeout = 0.2;

        $this->log("Enriching data for chunk identified by $id, waiting $timeout seconds before starting HTTP requests.");

        sleep($timeout);

        $chunk = $this->enrichWithViaf($chunk);

        $chunk = $this->enrichWithWiki($chunk);

        $chunk = $this->enrichWithRkd($chunk);

        $chunk = $this->mergeNameVariants($chunk);

        $chunk['phoneticCreator'] = array();

        if (!empty($chunk['creator'])) {
            // Add a phonetic equivalent of the creator
            foreach ($chunk['creator'] as $creator) {
                array_push($chunk['phoneticCreator'], soundex($creator));
            }
        }

        $this->log('----- Done mapping data -----');

        return $chunk;
    }

    /**
     * Merge the name variants from the different data sources
     *
     * @param array $chunk
     *
     * @return array
     */
    private function mergeNameVariants($chunk)
    {
        $chunk['uniqueNameVariants'] = array();

        // If the entry (rkd, wikidata, viaf) exists, search for nameVariants
        if (!empty($chunk['RKD']['uniqueNameVariants'])) {
            $chunk['uniqueNameVariants'] = array_merge($chunk['RKD']['uniqueNameVariants'], $chunk['uniqueNameVariants']);
        }

        if (!empty($chunk['Wikidata']['uniqueNameVariants'])) {
            $chunk['uniqueNameVariants'] = array_merge($chunk['Wikidata']['uniqueNameVariants'], $chunk['uniqueNameVariants']);
        }

        if (!empty($chunk['VIAF']['uniqueNameVariants'])) {
            $chunk['uniqueNameVariants'] = array_merge($chunk['VIAF']['uniqueNameVariants'], $chunk['uniqueNameVariants']);
        }

        $chunk['uniqueNameVariants'] = array_unique($chunk['uniqueNameVariants']);

        // array_unique leaves the original indexing [0] [4] ...
        // and this gets processed in the document store as an object, not as an array
        // for easy querying these non-sequential indexes need to be removed
        $nameVariants = array();

        foreach ($chunk['uniqueNameVariants'] as $index => $value) {
            array_push($nameVariants, $value);
        }

        $chunk['uniqueNameVariants'] = $nameVariants;

        // Count the amount of URI's it has
        $matches = array('creatorViafPid', 'creatorWikidataPid', 'creatorRkdPid', 'creatorOdisPid');

        $matchCount = 0;

        foreach ($matches as $match) {
            if (!empty($chunk[$match])) {
                $matchCount++;
            }
        }

        $chunk['matchedUris'] = $matchCount;

        return $chunk;
    }

    /**
     * Enrich the data in the chunk with data from the VIAF data source
     *
     * @param array $chunk
     *
     * @return array
     */
    private function enrichWithViaf($chunk)
    {
        if (!empty($chunk['creatorViafPid'])) {

            // Initalise the VIAF array
            $chunk['VIAF'] = array(
                                'preferredNames' => array(),
                                'nonPreferredNames' => array(),
                                'dateOfBirth' => array(),
                                'dateOfDeath' => array(),
                                'uniqueNameVariants' => array(),
                            );

            foreach ($chunk['creatorViafPid'] as $link) {

                try {

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $link . '/rdf.xml');
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $rdf = curl_exec($ch);

                    curl_close($ch);

                    $viafData = new \EasyRdf_Graph($link, $rdf, 'rdf');

                } catch (\EasyRdf_EXception $ex) {

                    $message = $ex->getMessage();

                    $id = $link;

                    $this->log("Couldn't load the link towards the RDF file: $id. Exception message was: $message.");

                    if (!empty($rdf)) {
                        $this->log("The rdf data we've gotten was: $rdf");
                    }

                    break;
                }

                // Add the "preferredNames" identified by skos:prefLabel
                $skosConcepts = $viafData->allOfType('skos:Concept');

                foreach ($skosConcepts as $skosConcept) {

                    array_push($chunk['VIAF']['preferredNames'], $skosConcept->getLiteral('skos:prefLabel')->getValue());

                }

                // Log the amount of preferred names added from the VIAF feed
                $prefNameCount = count($chunk['VIAF']['preferredNames']);

                $this->log("Added $prefNameCount preferred names from the VIAF RDF feed.");

                // Add the "nonPreferredNames" identified by skos:altLabel
                foreach ($skosConcepts as $skosConcept) {

                    $literals = $skosConcept->allLiterals('skos:altLabel');

                    foreach ($literals as $literal) {

                        array_push($chunk['VIAF']['nonPreferredNames'], $literal->getValue());

                    }

                }

                // Log the amount of non preferred name from the VIAF feed
                $nonPrefNamesCount = count($chunk['VIAF']['nonPreferredNames']);

                $this->log("Added $nonPrefNamesCount non preferred names from the VIAF RDF feed.");

                // Add the date of birth and data of death
                $description = $viafData->resource($link);

                \EasyRdf_Namespace::set('rdaGr2', 'http://rdvocab.info/ElementsGr2/');

                // EasyRdf lowercases all of his namespace prefixes!
                $dateOfBirth = $description->getLiteral('rdagr2:dateOfBirth');
                $dateOfDeath = $description->getLiteral('rdagr2:dateOfDeath');

                if (!empty($dateOfBirth)) {

                    array_push($chunk['VIAF']['dateOfBirth'], $dateOfBirth->getValue());
                    $this->log("Added the date of birth from the VIAF feed");

                } else {
                    $this->log("No date of birth found in the VIAF feed.");
                }

                if (!empty($dateOfDeath)) {

                    array_push($chunk['VIAF']['dateOfDeath'], $dateOfDeath->getValue());
                    $this->log("Added the date of death from the VIAF feed");

                } else {
                    $this->log("No date of death found in the VIAF feed.");
                }
            }

            // Put the unique name variants (non preferred and preferred names) into name variants
            $uniqueNameVariants = array();

            foreach ($chunk['VIAF']['preferredNames'] as $preferredName) {
                if (!in_array($preferredName, $uniqueNameVariants)) {
                    array_push($uniqueNameVariants, $preferredName);
                }
            }

            foreach ($chunk['VIAF']['nonPreferredNames'] as $nonPreferredName) {
                if (!in_array($nonPreferredName, $uniqueNameVariants)) {
                    array_push($uniqueNameVariants, $nonPreferredName);
                }
            }

            $chunk['VIAF']['uniqueNameVariants'] = $uniqueNameVariants;

        } else {

            $chunk['VIAF'] = array();

            $this->log("No creatorViafPid column was found in the data, returning data without VIAF enrichment.");
        }

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
        if (!empty($chunk['creatorWikidataPid'])) {

            $chunk['Wikidata'] = array(
                                    'placeOfBirth' => array(),
                                    'placeOfDeath' => array(),
                                    'dateOfBirth' => array(),
                                    'dateOfDeath' => array(),
                                    'nameVariants' => array(
                                        'en' => array(),
                                        'fr' => array(),
                                        'de' => array(),
                                        'nl' => array()
                                    ),
                                    'uniqueNameVariants' => array(),
                                );

            foreach ($chunk['creatorWikidataPid'] as $link) {

                // Get the Wikidata URI
                $wikiURI = $link;

                // Get the wiki data identifier
                $pieces = explode('/', $wikiURI);

                $wikiID = end($pieces);

                // Prepare the web scraper
                $client = new Client();

                try {

                    // Prepare the crawler
                    $crawler = $client->request('GET', $wikiURI);

                } catch (RequestException $ex) {

                    $this->log('An error has occurred while retrieving wiki data:' . $ex->getMessage(), 'error');

                    continue;
                }

                // Add the date of birth
                $dateOfBirth = $crawler->filter('div[id="P569"]');

                if (!empty($dateOfBirth)) {

                    try {

                        $dateOfBirth = $dateOfBirth->text();

                        // Clean up the text element
                        preg_match('/\s*(\d{2}\s[a-zA-Z]+\s\d{4})\s*/', $dateOfBirth, $matches);

                        array_push($chunk['Wikidata']['dateOfBirth'], @$matches[1]);

                    } catch (\InvalidArgumentException $ex) {
                        $this->log("No date of birth could be retrieved from the Wikidata website ($wikiURI).");
                    }
                }

                // Add the place of birth

                $placeOfBirth = $crawler->filter('div[id="P19"]');

                if (!empty($placeOfBirth)) {

                    try {

                        $placeOfBirth = $placeOfBirth->text();

                        // Clean up the text element
                        preg_match('/\s*(.*)\s*/', $placeOfBirth, $matches);

                        array($chunk['Wikidata']['placeOfBirth'], @$matches[1]);

                    } catch (\InvalidArgumentException $ex) {
                        $this->log("No place of birth could be retrieved from the Wikidata website ($wikiURI).");
                    }
                }

                // Add the date of death

                $dateOfDeath = $crawler->filter('div[id="P570"]');

                if (!empty($dateOfDeath)) {

                    try {

                        $dateOfDeath = $dateOfDeath->text();

                        // Clean up the text element
                        preg_match('/\s*(\d{2}\s[a-zA-Z]+\s\d{4})\s*/', $dateOfDeath, $matches);

                        array_push($chunk['Wikidata']['dateOfDeath'], @$matches[1]);

                    } catch (\InvalidArgumentException $ex) {
                        $this->log("No date of death could be retrieved from the Wikidata website ($wikiURI).");
                    }
                }

                // Add the place of death

                $placeOfDeath = $crawler->filter('div[id="P20"]');

                if (!empty($placeOfDeath)) {

                    try {

                        $placeOfDeath = $placeOfDeath->text();

                        // Clean up the text element
                        preg_match('/\s*(.*)\s*/', $placeOfDeath, $matches);

                        array_push($chunk['Wikidata']['placeOfDeath'], @$matches[1]);

                    } catch (\InvalidArgumentException $ex) {
                        $this->log("No place of death could be retrieved from the Wikidata website ($wikiURI).");
                    }
                }

                // Add the multi-lingual name variants, these have to be
                // fetched from a different URI with language query string options
                // the default language is english, so this name is already traceable in our crawler

                array_push($chunk['Wikidata']['nameVariants']['en'], $this->getWikiName($crawler, $wikiID));

                $languages = array('nl', 'fr', 'de');

                foreach ($languages as $language) {

                    // Prepare the web scraper
                    $client = new Client();

                    try {

                        // Prepare the crawler with the language options

                        $URI = $wikiURI . '?setlang=' . $language . '&uselang=' . $language;

                        $crawler = $client->request('GET', $URI);

                    } catch (RequestException $ex) {

                        $this->log('Something went wrong while fetching a lingual name variant:' . $ex->getMessage(), 'error');

                        continue;
                    }

                    $name = $this->getWikiName($crawler, $wikiID);

                    array_push($chunk['Wikidata']['nameVariants'][$language], $name);

                    // Add the name, if not already present, in the summary of the wiki name variants
                    if (!in_array($name, $chunk['Wikidata']['uniqueNameVariants'])) {
                        array_push($chunk['Wikidata']['uniqueNameVariants'], $name);
                    }

                    // Don't overload the Wiki with requests
                    sleep(0.5);
                }
            }

        } else {

            $chunk['Wikidata'] = array();

            $this->log("No Wikidata link was found, returning data without Wikidata enrichment.");
        }

        return $chunk;
    }

    /**
     * Fetch the name from the h1 title
     *
     * @param Symfony\Component\DomCrawler $crawler
     * @param int                          $wikiID
     *
     * @return string
     */
    private function getWikiName($crawler, $wikiID)
    {
        $name = $crawler->filter("h1[id=\"wb-firstHeading-$wikiID\"]");

        try {

            $name = $name->text();

            $pieces = explode("\n", $name);

            return $pieces[1];

        } catch (\InvalidArgumentException $ex) {

            $this->log("No lingual name variant found.");

            return "";
        }
    }

    /**
     * Enrich the data in the chunk with data from the RKD data source
     *
     * @param array $chunk
     *
     * @return array
     */
    private function enrichWithRkd($chunk)
    {
        if (!empty($chunk['creatorRkdPid'])) {

            $chunk['RKD'] = array(
                                'preferredNames' => array(),
                                'uniqueNameVariants' => array(),
                                'placeOfDeath' => array(),
                                'placeOfBirth' => array(),
                                'dateOfDeath' => array(),
                                'dateOfBirth' => array(),
                            );

            foreach ($chunk['creatorRkdPid'] as $link) {

                $website = $link;

                // Make a new web client
                $client = new Client();

                try {

                    // Prepare the crawler
                    $crawler = $client->request('GET', $website);

                } catch (RequestException $ex) {

                    $this->log('An error has occurred while retrieving rkd data:' . $ex->getMessage(), 'error');

                    continue;
                }

                // Scrape and add the preferred name
                $preferredName = $crawler->filter('div[class="record-metadata"]')->extract('data-title');

                // This is always one name, but for consistency reasons with other data enrichments
                // regarding preferred names, we'll add this one in an array as well
                array_push($chunk['RKD']['preferredNames'], @$preferredName[0]);

                $this->log("Added preferred name(s) from the RKD feed.");

                // Scrape and add the name variants
                $nameVariants = $crawler->filter(
                    'div.content.artistsdb-container
                    > div.row
                    > div.record-details
                    > div.content
                    > div.record
                    > div.left
                    > div.fieldGroup.expandable
                    > div.expandable-content
                    > dl
                    > dd'
                );

                // Name variants are listed with <br> tags and a div style tag,
                // not possible to filter through xpath
                try {

                    $nameVariants = $nameVariants->html();

                    $nameVariants = substr($nameVariants, 0, strpos($nameVariants, '<div'));

                    $nameVariants = rtrim($nameVariants);

                    foreach (explode('<br>', $nameVariants) as $nameVariant) {
                        array_push($chunk['RKD']['uniqueNameVariants'], $nameVariant);
                    }

                    $this->log("Added name variants from the RKD feed.");

                } catch (\InvalidArgumentException $ex) {
                    $this->log("Could not retrieve name variants from the RKD feed (none provided).");
                }

                // Scrape and add the date of birth + place
                $bio = $crawler->filter(
                    'div.content.artistsdb-container
                    > div.row
                    > div.record-details
                    > div.content
                    > div.record
                    > div.left
                    > div.fieldGroup
                    > dl
                    > dt
                    '
                );

                // Parse the data from the siblings of the dt nodes
                $bioData = $bio->each(function ($node) {

                    $bioData = array();

                    try {
                        if ($node->text() == 'Born') {

                            $bioData['placeOfBirth'] = $node->siblings()->filter('dd')->filter('a')->text();

                            $bioText = $node->siblings()->filter('dd')->text();

                            if (!empty($bioText)) {

                                preg_match('/.*\/(\d{4}-\d{2}-\d{2}).*/', $bioText, $matches);

                                $bioData['dateOfBirth'] = @$matches[1];
                            }

                        } else if ($node->text() == 'Deceased') {

                            $bioData['placeOfDeath'] = $node->siblings()->filter('dd')->filter('a')->text();

                            $bioText = $node->siblings()->filter('dd')->text();

                            if (!empty($bioText)) {

                                preg_match('/.*\/(\d{4}-\d{2}-\d{2}).*/', $bioText, $matches);

                                $bioData['dateOfDeath'] = @$matches[1];
                            }
                        }

                        if (!empty($bioData)) {
                            return $bioData;
                        }
                    } catch (\InvalidArgumentException $ex) {
                        // No need to log this, not all selected dt nodes contain text nodes
                    }
                });

                // Filter out empty values returned from the each() function above
                $bioData = array_filter($bioData);

                // Every node result is embedded in an array, remove this level
                foreach ($bioData as $bioDataArr) {

                    foreach ($bioDataArr as $key => $value) {
                        array_push($chunk['RKD'][$key], $value);
                    }
                }
            }

            // Make the name variants unique, and add the preferredNames to them for they are also considered name variants
            foreach ($chunk['RKD']['preferredNames'] as $preferredName) {
                array_push($chunk['RKD']['uniqueNameVariants'], $preferredName);
            }

            $uniqueNameVariants = array();

            foreach ($chunk['RKD']['uniqueNameVariants'] as $nameVariant) {
                if (!in_array($nameVariant, $uniqueNameVariants)) {
                    array_push($uniqueNameVariants, $nameVariant);
                }
            }

            $chunk['RKD']['uniqueNameVariants'] = $uniqueNameVariants;

        } else {

            $chunk['RKD'] = array();

            $this->log("No RKD link was found in the chunk, returning data without RKD enrichment.");
        }

        return $chunk;
    }
}

<?php

namespace Tdt\Input\EMLP\Map;

use Goutte\Client;

class Packedobject extends AMapper
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

        // All values are possible multi values
        foreach ($chunk as $key => $value) {

            if (!empty($value)) {
                $chunk[$key] = explode(';', $value);
            } else {
                $chunk[$key] = array();
            }
        }

        // Add the original data provider
        $chunk['dataprovider'] = $this->mapper->data_provider;

        $this->log('------   Mapping data  ------');

        $timeout = 0.1;

        $this->log("Enriching data for chunk identified by $id, waiting $timeout seconds before starting HTTP requests.");

        sleep($timeout);

        $chunk = $this->enrichWithAat($chunk);

        $this->log('----- Done mapping data -----');

        return $chunk;
    }



    /**
     * Enrich the data in the chunk with data from the AAT data source
     *
     * @param array $chunk
     *
     * @return array
     */
    private function enrichWithAat($chunk)
    {
        if (!empty($chunk['objectNameAatPid'])) {

            $chunk['AAT'] = array(
                                'preferredNames' => array(
                                    'nl' => array(),
                                    'en' => array(),
                                    'fr' => array(),
                                    'de' => array(),
                                ),
                                'nonPreferredNames' => array(
                                    'nl' => array(),
                                    'en' => array(),
                                    'fr' => array(),
                                    'de' => array(),
                                ),
                                'note' => array(),
                            );

            foreach ($chunk['objectNameAatPid'] as $link) {

                try {

                    $aatData = new \EasyRdf_Graph();

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'http://vocab.getty.edu/download/rdf?uri=' . $link . '.rdf');
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $rdf = curl_exec($ch);

                    curl_close($ch);

                    if (!empty($rdf)) {

                        $aatData = new \EasyRdf_Graph($link, $rdf, 'rdf');

                    } else {

                        $this->log('Failed to get data from the link: ' . $link . '.rdf', 'error');

                        continue;
                    }

                } catch (\EasyRdf_Exception $ex) {

                    $message = $ex->getMessage();

                    $this->log("Couldn't load the link to the RDF file: $link. Exception message: $message.", 'error');

                    if (!empty($rdf)) {
                        $this->log("The rdf data we've gotten was: $rdf");
                    }

                    continue;
                }

                // Fetch the nl scope note
                //(string $resource, string $propertyPath, string $type = null, string $lang = null)
                $labels = $aatData->allOfType('skosxl:Label');

                foreach ($labels as $label) {

                    $note = $label->getLiteral('skosxl:literalForm', 'nl');

                    if (!empty($note)) {

                        array_push($chunk['AAT']['note'], $note->getValue());

                        break;
                    }
                }

                // Add the en, de, fr, nl (non) preferred labels
                // These are linked with the skos:Concept (should be only one)
                $skosConcepts = $aatData->allOfType('skos:Concept');

                if (!empty($skosConcepts)) {

                    $skosConcept = array_shift($skosConcepts);

                    // Fetch and add the en, de, fr, nl preferred label (prefLabel)
                    $prefLabelNl = $skosConcept->getLiteral('skos:prefLabel', 'nl');

                    if (!empty($prefLabelNl)) {
                        array_push($chunk['AAT']['preferredNames']['nl'], $prefLabelNl->getValue());
                    }

                    $prefLabelFr = $skosConcept->getLiteral('skos:prefLabel', 'fr');

                    if (!empty($prefLabelFr)) {
                        array_push($chunk['AAT']['preferredNames']['fr'], $prefLabelFr->getValue());
                    }

                    $prefLabelEn = $skosConcept->getLiteral('skos:prefLabel', 'en');

                    if (!empty($prefLabelEn)) {
                        array_push($chunk['AAT']['preferredNames']['en'], $prefLabelEn->getValue());
                    }

                    $prefLabelDe = $skosConcept->getLiteral('skos:prefLabel', 'de');

                    if (!empty($prefLabelDe)) {
                        array_push($chunk['AAT']['preferredNames']['de'], $prefLabelDe->getValue());
                    }

                    // Fetch and add the en, de, fr, nl non preferred labels (altLabel)
                    $altLabelNl = $skosConcept->getLiteral('skos:altLabel', 'nl');

                    if (!empty($altLabelNl)) {
                        array_push($chunk['AAT']['nonPreferredNames']['nl'], $altLabelNl->getValue());
                    }

                    $altLabelFr = $skosConcept->getLiteral('skos:altLabel', 'fr');

                    if (!empty($altLabelFr)) {
                        array_push($chunk['AAT']['nonPreferredNames']['fr'], $altLabelFr->getValue());
                    }

                    $altLabelEn = $skosConcept->getLiteral('skos:altLabel', 'en');

                    if (!empty($altLabelEn)) {
                        array_push($chunk['AAT']['nonPreferredNames']['en'], $altLabelEn->getValue());
                    }

                    $altLabelDe = $skosConcept->getLiteral('skos:altLabel', 'de');

                    if (!empty($altLabelDe)) {
                        array_push($chunk['AAT']['nonPreferredNames']['de'], $altLabelDe->getValue());
                    }
                } else {
                    $this->log("Found no skos:Concept to extract label data from!");
                }

                sleep(0.5);
            }
        } else {
            $this->log("No link found to an AAT feed.");
        }

        return $chunk;
    }
}

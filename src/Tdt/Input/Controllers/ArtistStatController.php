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

class ArtistStatController extends StatController
{

    protected static $COLLECTION = 'artists';

    public function handle($dataProvider = null)
    {

        $data = array();

        // Fetch information about the artists

        // How many unique strings were delivered by the providers
        // Note: We don't take into account the links to the 3rd party data

        // Set up a connection to the mongodb, we cannot perform queries with
        // the mongoDB abstraction from the jenssegers repo
        // (e.g. distinct() function already provides incorrect information)

        $client = $this->getMongoClient();

        // Select the artist collection
        $artists = $client->selectCollection(self::$DB_NAME, self::$COLLECTION);

        // Get the amount of unique strings in the original data
        $data['uniqueStrings'] = $this->countUniqueStrings($artists);

        // Calculate per feed how many unique agents were identified
        $data['rkdAgents'] = $this->countRkdUris($artists);
        $data['rkdUniqueAgents'] = $this->countUniqueRkdUris($artists);

        $data['viafAgents'] = $this->countViafUris($artists);
        $data['viafUniqueAgents'] = $this->countUniqueViafUris($artists);

        $data['wikidataAgents'] = $this->countWikidataUris($artists);
        $data['wikidataUniqueAgents'] = $this->countUniqueWikiUris($artists);

        $data['odisAgents'] = $this->countOdisUris($artists);
        $data['odisUniqueAgents'] = $this->countUniqueOdisUris($artists);

        // Count the creators that were matched with 1, 2 and 3 URI's
        $data['matchOneURI'] = $this->countUriMatch($artists, 1);
        $data['matchTwoURI'] = $this->countUriMatch($artists, 2);
        $data['matchThreeURI'] = $this->countUriMatch($artists, 3);
        $data['matchFourURI'] = $this->countUriMatch($artists, 4);

        // Get the amount of unique name variants
        // The amount of unique name variants from the different feeds is extra info
        // The most important part is the total name variants count
        $nameVariants = $this->countNameVariants($artists);

        $wikiNameVariants = $this->countWikiNameVariants($artists);

        $data['rkdNameVariants'] = $this->countRkdNameVariants($artists);

        $data['viafNameVariants'] = $this->countViafNameVariants($artists);

        $data['wikiNameVariants'] = $this->countWikiNameVariants($artists);

        $data['nameVariants'] = $nameVariants;

        return \Response::json($data);
    }

    /**
     * Count the amount of unique strings
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countUniqueStrings($artists)
    {
        $unwind = array('$unwind' => '$creator');

        $group = array(
                    '$group' => array(
                        '_id' => '$creator',
                        'unique' => array(
                            '$addToSet' => '$creator'
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

        $result = $artists->aggregate($unwind, $group, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of creators that match with an amount of provided URI's
     *
     * @param MongoCollection $artists
     * @param int             $amount
     *
     * @return int
     */
    private function countUriMatch($artists, $amount)
    {
        return $artists->count(array('matchedUris' => $amount));
    }

    /**
     * Count the amount of RKD URI's in the artists collection
     *
     * @param MongoCollection $artists
     *
     * @return in
     */
    private function countRkdUris($artists)
    {
        $unwind = array('$unwind' => '$creatorRkdPid');

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $artists->aggregate($unwind, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of Wikidata URI's in the artists collection
     *
     * @param MongoCollection $artists
     *
     * @return in
     */
    private function countWikidataUris($artists)
    {
        $unwind = array('$unwind' => '$creatorWikidataPid');

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $artists->aggregate($unwind, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of VIAF URI's in the artists collection
     *
     * @param MongoCollection $artists
     *
     * @return in
     */
    private function countViafUris($artists)
    {
        $unwind = array('$unwind' => '$creatorViafId');

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $artists->aggregate($unwind, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of Odis URI's in the artists collection
     *
     * @param MongoCollection $artists
     *
     * @return in
     */
    private function countOdisUris($artists)
    {
        $unwind = array('$unwind' => '$creatorOdisPid');

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $artists->aggregate($unwind, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of unique RKD URI's to identify the amount of
     * unique agents through RKD
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countUniqueRkdUris($artists)
    {
        $unwind = array('$unwind' => '$creatorRkdPid');

        $group = array(
                    '$group' => array(
                        '_id' => '$creatorRkdPid',
                        'unique' => array(
                            '$addToSet' => '$creatorRkdPid'
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

        $result = $artists->aggregate($unwind, $group, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of unique VIAF URI's to identify the amount of
     * unique agents through VIAF
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countUniqueViafUris($artists)
    {
        $unwind = array('$unwind' => '$creatorViafId');

        $group = array(
                    '$group' => array(
                        '_id' => '$creatorViafId',
                        'unique' => array(
                            '$addToSet' => '$creatorViafId'
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

        $result = $artists->aggregate($unwind, $group, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of unique Odis URI's to identify the amount of
     * unique agents through Odis
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countUniqueOdisUris($artists)
    {
        $unwind = array('$unwind' => '$creatorOdisPid');

        $group = array(
                    '$group' => array(
                        '_id' => '$creatorOdisPid',
                        'unique' => array(
                            '$addToSet' => '$creatorOdisPid'
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

        $result = $artists->aggregate($unwind, $group, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of unique Wikidata URI's to identify the amount of
     * unique agents through Wikidata
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countUniqueWikiUris($artists)
    {
        $unwind = array('$unwind' => '$creatorWikidataPid');

        $group = array(
                    '$group' => array(
                        '_id' => '$creatorWikidataPid',
                        'unique' => array(
                            '$addToSet' => '$creatorWikidataPid'
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

        $result = $artists->aggregate($unwind, $group, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Get the amount of unique name variants from Wikidata
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countWikiNameVariants($artists)
    {
        $unwind = array('$unwind' => '$Wikidata.uniqueNameVariants');

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $artists->aggregate($unwind, $count);

        return $result['result'][0]['count'];

    }

    /**
     * Get the amount of unique name variants from VIAF (=preferredNames)
     *
     * TODO only fetch documents where the namevariants are set!
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countViafNameVariants($artists)
    {
        $unwind = array('$unwind' => '$VIAF.uniqueNameVariants');

        $count = array(
                    '$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                );

        $result = $artists->aggregate($unwind, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Get the amount of unique name variants from RKD
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countRkdNameVariants($artists)
    {
        $project = array('$project' => array('RKD' => 1, 'creator' => 1));

        $unwind = array('$unwind' => '$RKD.uniqueNameVariants');

        $count = array(
                        '$group' => array(
                            '_id' => null,
                            'count' => array(
                                '$sum' => 1
                            )
                        )
                    );

        $result = $artists->aggregate($project, $unwind, $count);

        return $result['result'][0]['count'];
    }

    /**
     * Count the amount of name variants
     *
     * These name variants are already uniquely added during the mapping process
     *
     * @param MongoCollection $artists
     *
     * @return int
     */
    private function countNameVariants($artists)
    {
        $unwind = array('$unwind' => '$uniqueNameVariants');

        $count = array('$group' => array(
                        '_id' => null,
                        'count' => array(
                            '$sum' => 1
                            ),
                        )
                    );

        $result = $artists->aggregate(array($unwind, $count));

        return $result['result'][0]['count'];
    }
}

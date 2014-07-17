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

class StatController extends \Controller
{

    public function handle($dataProvider)
    {

        // Fetch information about the artists

        // How many unique strings were delivered by the provider
        // Note: We don't take into account the links to the 3rd party data


        return "hi";
    }
}

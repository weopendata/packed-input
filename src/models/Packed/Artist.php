<?php

namespace Packed;

use Jenssegers\Mongodb\Model as Eloquent;

class Artist extends Eloquent
{
    protected $collection = 'artists';

    protected $guarded = array();

    protected $connection = 'mongodb';
}

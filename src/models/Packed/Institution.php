<?php

namespace Packed;

use Jenssegers\Mongodb\Model as Eloquent;

class Institution extends Eloquent
{

    protected $collection = 'institutions';

    protected $guarded = array();

    protected $connection = 'mongodb';
}

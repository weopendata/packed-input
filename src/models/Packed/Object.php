<?php

namespace Packed;

use Jenssegers\Mongodb\Model as Eloquent;

class Object extends Eloquent
{

    protected $collection = 'objects';

    protected $guarded = array();

    protected $connection = 'mongodb';
}

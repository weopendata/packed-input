<?php

namespace Load;

use Eloquent;

/**
 * Packed load model
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Packedartist extends Eloquent
{

    protected $table = 'input_packedload';

    protected $fillable = array('data_provider');

    /**
     * Relationship with Job
     */
    public function job()
    {
        return $this->morphOne('Job', 'loader');
    }

    /**
     * Validate the input for this model and related models.
     */
    public static function validate($params)
    {

        $packedParams = array_only($params, array_keys(self::getCreateProperties()));

        return parent::validate($packedParams);
    }

    /**
     * Retrieve the set of create parameters that make up a CSV definition.
     * Include the parameters that make up relationships with this model.
     */
    public static function getAllProperties()
    {
        return self::getCreateProperties();
    }

    /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators()
    {
        return array(
            'data_provider' => 'required',
        );
    }

    /**
     * Return the properties ( = column fields ) for this model.
     */
    public static function getCreateProperties()
    {

        return array(
            'data_provider' => array(
                'required' => true,
                'description' => 'The name of the provider of the data.',
            ),
        );
    }
}

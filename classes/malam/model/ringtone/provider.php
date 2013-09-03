<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Model_Ringtone_Provider extends ORM
{
    /**
     * Table name
     * @var string
     */
    protected $_table_name      = 'relationship_providers';

    /**
     * "Belongs to" relationships
     *
     * @var array
     */
    protected $_belongs_to      = array(
        'ringtone'      => array('model' => 'ringtone'),
        'provider'      => array('model' => 'provider'),
    );

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'provider_id' => array(
                array('not_empty'),
            ),
            'ringtone_id' => array(
                array('not_empty'),
            ),
            'command' => array(
                array('not_empty'),
                array('max_length', array(':value', 50))
            ),
            'number' => array(
                array('digit'),
                array('numeric'),
            ),
        );
    }

    /**
     * Filter definitions for validation
     *
     * @return array
     */
    public function filters()
    {
        return array(
            'provider_id' => array(
                array('ORM::Check_Model', array(':value', 'provider'))
            ),
            'ringtone_id' => array(
                array('ORM::Check_Model', array(':value', 'ringtone'))
            ),
        );
    }
}
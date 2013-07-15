<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Model_Rbt extends ORM
{
    /**
     * Admin route name
     * @var string
     */
    protected $_admin_route_name = 'admin-rbt';

    /**
     * Route name
     * @var string
     */
    protected $_route_name      = 'rbt';

    /**
     * Table name
     * @var string
     */
    protected $_table_name      = 'relationship_rbt';

    /**
     * "Belongs to" relationships
     *
     * @var array
     */
    protected $_belongs_to      = array(
        'band'          => array('model' => 'band'),
        'user'          => array('model' => 'user'),
    );

    /**
     * Auto-update columns for creation
     *
     * @var string
     */
    protected $_created_column  = array(
        'column'        => 'created_at',
        'format'        => 'Y-m-d H:i:s'
    );

    /**
     * @var array
     */
    protected $_sorting         = array(
        'created_at'    => 'DESC'
    );

    /**
     * Name field
     *
     * @var string
     */
    protected $name_field       = 'title';

    /**
     * Band
     *
     * @var Model_Band
     */
    protected $_band            = NULL;

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'user_id' => array(
                array('not_empty'),
            ),
            'band_id' => array(
                array('not_empty'),
            ),
            'title' => array(
                array('not_empty'),
                array('max_length', array(':value', 100))
            ),
            'provider' => array(
                array('not_empty'),
                array('max_length', array(':value', 25))
            ),
            'command' => array(
                array('not_empty'),
                array('max_length', array(':value', 100))
            ),
            'number' => array(
                array('not_empty'),
                array('max_length', array(':value', 10))
            ),
            'state' => array(
                array('ORM::Validation_State')
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
            'user_id' => array(
                array('ORM::Validation_State', array(':value', 'user'))
            ),
            'band_id' => array(
                array('ORM::Validation_State', array(':value', 'band'))
            ),
            'title' => array(
                array('trim'),
            ),
            'provider' => array(
                array('trim'),
            ),
            'number' => array(
                array('trim'),
            ),
            'command' => array(
                array('trim'),
            ),
            'is_featured' => array(
                array(array($this, 'Filter_Is_Featured'))
            ),
        );
    }

    public function to_paginate()
    {
        return Paginate::factory($this)
            ->sort('created_at', Paginate::SORT_DESC)
            ->columns(array($this->primary_key(), 'title', 'provider', 'command', 'number'))
            ->search_columns(array('title', 'provider'));
    }

    public function get_field($field)
    {
        switch (strtolower($field)):
            case 'command':
                return htmlspecialchars($this->$field);
                break;

            case 'title':
                return $this->admin_update_url($this->name());
                break;

            default :
                return parent::get_field($field);
                break;
        endswitch;
    }

    /**
     * Set values from an array with support for one-one relationships.  This method should be used
     * for loading in post data, etc.
     *
     * @param  array $values   Array of column => val
     * @param  array $expected Array of keys to take from $values
     * @return ORM
     */
    public function values(array $values, array $expected = NULL)
    {
        if (NULL === $expected || empty($expected))
        {
            $expected = array('band_id', 'title', 'provider', 'number',
                              'command', 'user_id', 'is_featured', 'state');
        }

        return parent::values($values, $expected);
    }

    public function set_band(Model_Band $band)
    {
        $this->_band = $band;
        $this->where('band_id', '=', $band->pk());
        return $this;
    }

    protected function link($action = 'index', $title = NULL, array $params = NULL, array $attributes = NULL)
    {
        empty($params) && $params = array();

        $band_id = $this->loaded() ? $this->band->pk() : $this->_band->pk();
        $params += array('band_id' => $band_id);

        return parent::link($action, $title, $params, $attributes);
    }
}

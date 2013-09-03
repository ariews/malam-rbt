<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Model_Provider extends ORM
{
    /**
     * Admin route name
     * @var string
     */
    protected $_admin_route_name = 'admin-provider';

    /**
     * Table name
     * @var string
     */
    protected $_table_name      = 'providers';

    /**
     * Name field
     *
     * @var string
     */
    protected $name_field       = 'name';

    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_many        = array(
        'ringtones'     => array(
            'model'         => 'ringtone',
            'through'       => 'relationship_providers',
        ),
        'ringtone_providers' => array(
            'model'         => 'ringtone_provider'
        ),
    );

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('max_length', array(':value', 50)),
                array(array($this, 'unique'), array('name', ':value')),
            ),
        );
    }

    public function values(array $values, array $expected = NULL)
    {
        if (NULL === $expected || empty($expected))
        {
            $expected = array('id', 'name');
        }

        return parent::values($values, $expected);
    }

    public function count_all_relationships()
    {
        $count = 0;

        foreach ($this->has_many() as $relation => $array)
        {
            $count += $this->$relation->find_all()->count();
        }

        return $count;
    }

    public function to_paginate()
    {
        return Paginate::factory($this)
            ->columns(array('id', 'name'))
            ->search_columns(array('name'));
    }

    public function get_field($field)
    {
        switch (strtolower($field)):
            case 'name':
                return $this->admin_update_url($this->name());
                break;

            default :
                return parent::get_field($field);
                break;
        endswitch;
    }

    protected function prepare_menu()
    {
        $menu = array(
            array(
                'title' => __(ORM::capitalize_title($this->object_name())),
                'url'   => $this->admin_index_url_only(),
            ),
            array(
                'title' => __($this->loaded() ? 'Update' : 'Add'),
                'url'   => $this->loaded()
                            ? $this->admin_update_url_only()
                            : $this->admin_create_url_only()
            ),
        );

        $this->_admin_menu = $menu;
    }
}
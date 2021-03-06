<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Model_Provider extends ORM
{
    /**
     * Name field
     *
     * @var string
     */
    protected $_name_field      = 'name';

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

    protected $_psearch_columns = array('name');

    protected $_ptable_columns  = array('id', 'name');

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

        foreach (array_keys($this->has_many()) as $relation)
        {
            $count += $this->$relation->find_all()->count();
        }

        return $count;
    }

    public function get_field($field)
    {
        switch (strtolower($field)):
            case 'name':    return $this->admin_update_url($this->name());
            default :       return parent::get_field($field);
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
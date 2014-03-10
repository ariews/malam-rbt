<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Model_Ringtone extends ORM
{
    /**
     * Table name
     * @var string
     */
    protected $_table_name      = 'ringtones';

    /**
     * "Belongs to" relationships
     *
     * @var array
     */
    protected $_belongs_to      = array(
        'band'          => array('model' => 'band'),
        'user'          => array('model' => 'user'),
        'ringtone_file' => array('model' => 'file_ringtone', 'foreign_key' => 'file_id'),
    );

    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_many        = array(
        'providers'     => array(
            'model'         => 'provider',
            'through'       => 'relationship_providers',
        ),
        'ringtone_providers' => array(
            'model'         => 'ringtone_provider'
        ),
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
    protected $_name_field      = 'title';

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
                array('ORM::Check_Model', array(':value', 'user'))
            ),
            'band_id' => array(
                array('ORM::Check_Model', array(':value', 'band'))
            ),
            'title' => array(
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
            ->columns(array($this->primary_key(), 'title', 'list ringtones', 'state'))
            ->search_columns(array('title'));
    }

    public function ringtones_list($file = 'ringtone/_list_provider', $theme = NULL)
    {
        if (NULL === $theme)
        {
            $theme = Kohana::$config->load('site.ui.admin');
        }

        $view = Malam_View::factory();
        $view->set_filename($file);
        $view->set_theme($theme);
        $view->set(array(
            'rproviders' => $this->ringtone_providers->find_all()
        ));

        return $view->render();
    }

    public function get_field($field)
    {
        switch (strtolower($field)):
            case 'title':
                return $this->admin_update_url($this->name());
                break;

            case 'list ringtones':
                return $this->ringtones_list();
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
            $expected = array('band_id', 'title', 'user_id', 'is_featured', 'state', 'file_id');
        }

        return parent::values($values, $expected);
    }

    public function set_band(Model_Band $band)
    {
        $this->_band = $this->band = $band;
        return $this;
    }

    protected function link($action = 'index', $title = NULL, array $params = NULL, array $attributes = NULL, array $query = NULL)
    {
        empty($params) && $params = array();

        $band_id = $this->loaded() ? $this->band->pk() : $this->_band->pk();
        $params += array('band_id' => $band_id);

        return parent::link($action, $title, $params, $attributes, $query);
    }

    protected function prepare_menu()
    {
        $menu = array(
            array(
                'title' => __(ORM::capitalize_title($this->band->object_name())),
                'url'   => $this->band->admin_update_url_only(),
            ),
            array(
                'title' => __('Ringtones'),
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

    public function create_or_update(array $data)
    {
        $file = Arr::get($data, 'file');
        if (! empty($file) && Upload::valid($file) && Upload::not_empty($file))
        {
            $file = ORM::factory('file_ringtone')
                    ->save_from_post($data);

            if ($file->loaded())
            {
                $data['file_id'] = $file->pk();
            }

            unset($data['file']);
        }

        $return = parent::create_or_update($data);

        $rproviders = Arr::get($data, 'rproviders');
        $providers  = array();

        foreach ($rproviders as $key => $value)
        {
            foreach ($value as $k => $v)
            {
                switch (strtolower($key)){
                    case 'ringtone_id':
                        $v = $return->pk();
                        break;
                    case 'provider_id':
                        $v = ORM::Check_Model($v, 'provider');
                        break;
                    default:
                        break;
                }

                $providers[$k][$key] = $v;
            }
        }

        $return->remove('providers');

        foreach ($providers as $k => $values)
        {
            $rp = ORM::factory('ringtone_provider');
            $rp->create_or_update($values);
        }

        return $return;
    }
}

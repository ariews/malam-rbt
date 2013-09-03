<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Controller_Admin_Provider extends Controller_Abstract_Bigcontent
{
    /**
     * Tag
     *
     * @var Model_Tag
     */
    protected $model            = 'provider';

    public function action_index()
    {
        $this->title('Provider index');
    }

    public function action_create()
    {
        $this->title('Create Provider');
    }

    public function action_update()
    {
        $this->title('Update Provider');
    }

    public function action_delete()
    {
        $this->title('Delete Provider');
    }
}
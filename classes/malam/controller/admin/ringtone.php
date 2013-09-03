<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Controller_Admin_Ringtone extends Controller_Abstract_Bigcontent
{
    /**
     * Ringtone
     *
     * @var Model_Ringtone
     */
    protected $model            = 'ringtone';

    /**
     * Band
     *
     * @var Model_Band
     */
    protected $band;

    public function action_index()
    {
        $this->title('Ringtones Index');
    }

    public function action_create()
    {
        $this->title('Create Ringtone');
    }

    public function action_delete()
    {
        $this->title('Delete Ringtone');
    }

    public function action_update()
    {
        $this->title('Update Ringtone');
    }

    public function __construct(Request $request, Response $response)
    {
        $this->band = ORM::factory('band', $request->param('band_id'));

        if (! $this->band->loaded())
        {
            throw new HTTP_Exception_404();
        }

        $this->model = $this->band->ringtones;

        parent::__construct($request, $response);
    }

    protected function _create_or_update()
    {
        parent::_create_or_update();

        $this->temporary->set(array(
            'providers'          => ORM::factory('provider')->find_all(),
            'ringtone_providers' => $this->model->ringtone_providers->find_all(),
            'ringtone_file'      => $this->model->ringtone_file
        ));
    }

    public function before()
    {
        parent::before();

        $this->temporary->band = $this->band;
        $this->menu
            ->set_current($this->band->admin_index_url_only());
    }
}
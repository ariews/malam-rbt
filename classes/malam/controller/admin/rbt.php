<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Controller_Admin_Rbt extends Controller_Abstract_Bigcontent
{
    /**
     * Rbt
     *
     * @var Model_Rbt
     */
    protected $model            = 'rbt';

    /**
     * Band
     *
     * @var Model_Band
     */
    protected $band;

    public function action_index()
    {
        $this->title(':band RBT Index', array(
            ':band' => $this->band->name()
        ));
    }

    public function action_create()
    {
        $this->title('Create :band RBT', array(
            ':band' => $this->band->name()
        ));
    }

    public function action_delete()
    {
        $this->title('Delete :band RBT', array(
            ':band' => $this->band->name()
        ));
    }

    public function action_update()
    {
        $this->title('Update :band RBT', array(
            ':band' => $this->band->name()
        ));
    }

    public function __construct(Request $request, Response $response)
    {
        $this->band = ORM::factory('band', $request->param('band_id'));

        if (! $this->band->loaded())
        {
            throw new HTTP_Exception_404();
        }

        if (! ($this->model instanceof ORM) && is_string($this->model))
        {
            $this->model = ORM::factory($this->model)->set_band($this->band);
        }

        parent::__construct($request, $response);
    }

    protected function _create_or_update()
    {
        $this->template = 'create';

        if ($this->is_post())
        {
            try {
                $this->model->create_or_update($this->post_data);
                $this->to_index();
            } catch (ORM_Validation_Exception $e)
            {
                $this->temporary->set(array(
                    'errors' => $e->errors('orm-rbt')
                ));
            }
        }
    }

    public function before()
    {
        parent::before();

        $this->temporary->band = $this->band;
        $this->menu
            ->set_current($this->band->admin_index_url_only());
    }
}
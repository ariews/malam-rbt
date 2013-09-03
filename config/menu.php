<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

$p  = ORM::factory('provider');

return array(
    'admin' => array(
        // ADMIN-PROVIDER
        500    => array(
            'title'     => __('Providers'),
            'url'       => $p->admin_index_url_only(),
        ),
    ),
);
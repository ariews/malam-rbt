<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

/* Dashboard Prefix */
$DPRX = Kohana::$config->load('site.dashboard_prefix');

return array(
    // Ringtone ----------------------------------------------------------------
    'ringtone'              => array(
        'uri_callback'      => 'ringtones/<action>(/<id>/<slug>)',
        'regex'             => array(
            'id'            => '\d+',
            'slug'          => '[a-zA-Z0-9-_]+',
            'action'        => 'read|index'
        ),
        'defaults'          => array(
            'controller'    => 'ringtone',
            'action'        => 'index',
            'id'            => NULL,
            'slug'          => NULL,
        )
    ),

    'admin-ringtone'        => array(
        'uri_callback'      => $DPRX.'ringtones/<band_id>/<action>(/<id>)',
        'regex'             => array(
            'action'        => 'index|create|delete|update|read',
            'id'            => '\d+',
            'band_id'       => '\d+',
        ),
        'defaults'          => array(
            'controller'    => 'ringtone',
            'directory'     => 'admin',
            'action'        => 'index',
            'id'            => NULL,
        )
    ),

    // Provider ----------------------------------------------------------------
    'admin-provider'        => array(
        'uri_callback'      => $DPRX.'providers/<action>(/<id>)',
        'regex'             => array(
            'action'        => 'index|create|delete|update|read',
            'id'            => '\d+',
        ),
        'defaults'          => array(
            'controller'    => 'provider',
            'directory'     => 'admin',
            'action'        => 'index',
            'id'            => NULL,
        )
    ),
);

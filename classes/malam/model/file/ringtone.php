<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Model_File_Ringtone extends Model_File
{
    protected $_is_direct_call  = FALSE;

    public function file_accept()
    {
        $config = $this->_config['upload']['accept'];
        return $config['audio'];
    }
}
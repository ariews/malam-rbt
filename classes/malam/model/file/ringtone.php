<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

abstract class Malam_Model_File_Ringtone extends Model_File
{
    public function file_accept()
    {
        $config = $this->_config['upload']['accept'];
        return $config['audio'];
    }
}
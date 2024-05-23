<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Sending Email library
 *	Created by Muammar Kadafi
 *
 */
class Sending_email
{
    function __construct()
    {
        $this->system = &get_instance();
    }
}

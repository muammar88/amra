<?php

/**
 *  -----------------------
 *	Model landing pages
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_landing_pages extends CI_Model
{
    // private $company_id;
    private $status;
    private $content;

    public function __construct()
    {
        parent::__construct();
        // $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
    }
}

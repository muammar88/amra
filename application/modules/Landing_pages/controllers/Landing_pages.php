<?php

/**
 *  -----------------------
 *	Landing Pages Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Landing_pages extends CI_Controller
{

    private $company_code;
    private $company_id;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        # Load user model
        $this->load->model('Model_landing_pages', 'model_landing_Pages');
        # set date timezone
        ini_set('date.timezone', 'Asia/Jakarta');
    }

    function index()
    {
        $data = array();
        $data['title'] = 'AMRa | Aplikasi Manajemen Travel Haji dan Umrah';
        $this->templating->landing_templating($data);
    }
}

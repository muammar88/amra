<?php

/**
 *  -----------------------
 *	User Token Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Token extends CI_Controller
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
		$this->load->model('Model_api', 'model_api');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}


}

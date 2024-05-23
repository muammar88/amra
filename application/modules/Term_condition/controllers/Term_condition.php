<?php

/**
 *  -----------------------
 *	Users Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Term_condition extends CI_Controller
{

	private $company_code;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_term_condition', 'model_term_condition');
		// # checking is not Login
		// $this->auth_library->Is_not_login();
		// # receive company code value
		// $this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	public function index(){

		if ($this->input->get('code')) {
			$code = htmlspecialchars($this->input->get('code'), ENT_QUOTES, 'UTF-8');

			// echo $code;
			# filter
			if( $this->model_term_condition->check_code( $code ) ) {
				# 
				$data = $this->model_term_condition->get_info_company( $code );

				$this->templating->term_condition_templating($data);
			}else{
				$this->templating->error_templating(array());
			}
		}
	}

}